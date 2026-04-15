<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // show search form
    public function index()
    {
        $events = Event::orderByDesc('starts_at')->where('status', 'finished')->get();
        return view('certificates.index', compact('events'));
    }

    // search tickets by phone or email
    public function search(Request $request)
    {
        $data = $request->validate([
            'query' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        $q = $data['query'];

        $tickets = Ticket::with('event')
            ->when($data['event_id'] ?? null, function($builder, $eventId){
                return $builder->where('event_id', $eventId);
            })
            ->where(function($b) use ($q){
                $b->where('phone', $q)
                  ->orWhere('email', $q);
            })
            ->where('checked_in_at', '!=', null)
            ->get();

        return view('certificates.results', compact('tickets', 'q'));
    }

    // generate certificate image and download, increment counter
    public function download(Ticket $ticket)
    {
        $event = $ticket->event;

        if (! $event || ! $event->certificate_image) {
            return abort(404, 'Certificate template not configured for this event');
        }

        $storage = Storage::disk('public');
        $bgPath = $storage->path($event->certificate_image);

        // try to create image resource
        $ext = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $img = @imagecreatefrompng($bgPath);
        } elseif (in_array($ext, ['jpg','jpeg'])) {
            $img = @imagecreatefromjpeg($bgPath);
        } elseif ($ext === 'webp') {
            $img = @imagecreatefromwebp($bgPath);
        } else {
            $img = @imagecreatefromstring(file_get_contents($bgPath));
        }

        if (! $img) {
            return abort(500, 'Unable to open certificate template');
        }

        // determine font color (hex) and allocate
        $hex = $event->certificate_font_color ?? '#000000';
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $textColor = imagecolorallocate($img, $r, $g, $b);

        $name = $ticket->name;

        // font handling: if provided, use uploaded font, else use system font fallback
        $fontPath = null;
        if ($event->certificate_font && $storage->exists($event->certificate_font)) {
            $fontPath = $storage->path($event->certificate_font);
        }

        // determine font size and text position from event settings (percent)
        $imgW = imagesx($img);
        $imgH = imagesy($img);

        $fontSize = intval($event->certificate_font_size ?? 36);
        $xPct = is_numeric($event->certificate_text_x_pct) ? floatval($event->certificate_text_x_pct) : 50.0;
        $yPct = is_numeric($event->certificate_text_y_pct) ? floatval($event->certificate_text_y_pct) : 60.0;

        // compute center point in pixels
        $centerX = intval($imgW * ($xPct / 100.0));
        $centerY = intval($imgH * ($yPct / 100.0));

        if ($fontPath && function_exists('imagettftext') && function_exists('imagettfbbox')) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
            $textW = abs($bbox[2] - $bbox[0]);
            $textH = abs($bbox[7] - $bbox[1]);

            $x = intval($centerX - ($textW / 2));
            // baseline: move down half text height so center matches
            $y = intval($centerY + ($textH / 2));
            imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontPath, $name);
        } else {
            // fallback to built-in fonts
            $font = 5; // built-in font size
            $textW = imagefontwidth($font) * strlen($name);
            $x = intval(($imgW - $textW) / 2);
            $y = intval($imgH * 0.6);
            imagestring($img, $font, $x, $y, $name, $textColor);
        }

        ob_start();
        // output as PNG to preserve transparency if any
        imagepng($img);
        $contents = ob_get_clean();
        imagedestroy($img);

        // increment counter on the ticket
        $ticket->increment('certificate_downloads');

        $filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $ticket->name) . '_certificate.png';

        return response($contents, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
