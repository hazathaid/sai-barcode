<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Ticket;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\ImageRenderer;

class BarcodeController extends Controller
{
    // Search by email+phone. If multiple matches, return JSON list with links.
    public function index(Request $request)
    {
        // If no query parameters, show search form
        if (! $request->has('email') && ! $request->has('phone')) {
            return view('barcode.form');
        }

        $data = $request->validate([
            'email' => 'nullable|email|required_without:phone',
            'phone' => 'nullable|string|required_without:email',
        ]);

        $query = Ticket::with('event');
        if (! empty($data['email'])) {
            $query->where('email', $data['email']);
        }
        if (! empty($data['phone'])) {
            $query->where('phone', $data['phone']);
        }

        $tickets = $query->get();

        if ($tickets->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tidak ditemukan'], 404);
            }
            return response()->view('barcode.notfound', ['email' => $data['email'], 'phone' => $data['phone']], 404);
        }

        if ($tickets->count() === 1) {
            $ticket = $tickets->first();
            if ($request->wantsJson()) {
                return redirect()->route('barcode.show', ['ticket' => $ticket->id]);
            }
            return view('barcode.show', ['ticket' => $ticket]);
        }

        // multiple matches
        if ($request->wantsJson()) {
            $list = $tickets->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name ?? null,
                    'event' => $t->event_id ?? null,
                    'code' => $t->code ?? null,
                    'barcode_url' => url('/barcode/'.$t->id)
                ];
            });
            return response()->json(['matches' => $list]);
        }

        return view('barcode.matches', ['tickets' => $tickets]);
    }

    // Return PNG barcode for a specific ticket id
    public function show($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if (! $ticket) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $payload = $ticket->id . '|' . ($ticket->code ?? $ticket->id);

        // Use SVG renderer backend from bacon-qr-code (no imagick required)
        $size = 300;
        $style = new RendererStyle($size, 4);
        $backend = new SvgImageBackEnd();
        $renderer = new ImageRenderer($style, $backend);
        $writer = new Writer($renderer);
        $svg = $writer->writeString($payload);

        return new Response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
