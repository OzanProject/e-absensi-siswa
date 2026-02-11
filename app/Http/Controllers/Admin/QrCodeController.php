<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class QrCodeController extends Controller
{
  /**
   * Display the dynamic QR Code page for Admin/Kiosk.
   */
  public function index()
  {
    return view('admin.qrcode.index');
  }

  /**
   * API to fetch the latest QR Code content.
   * Called via AJAX every few seconds.
   */
  public function generate()
  {
    // 1. Create a secure token containing timestamp and a random string
    $data = [
      'timestamp' => Carbon::now()->timestamp,
      'nonce' => Str::random(10),
      'school_id' => 1, // Optional: if multi-tenant
    ];

    // 2. Encrypt the data so it cannot be forged easily
    // We use JSON encode and then standard Laravel encryption
    $token = encrypt(json_encode($data));

    // 3. Cache valid token for a short duration (e.g., 30 seconds)
    // This allows the backend to validate if the token is fresh
    Cache::put('qr_token_' . $data['nonce'], $token, 30);

    // 4. Return the QR Code SVG
    $qrCode = QrCode::size(300)->generate($token);

    return response()->json([
      'qr_code' => (string) $qrCode,
      'token' => $token
    ]);
  }
}
