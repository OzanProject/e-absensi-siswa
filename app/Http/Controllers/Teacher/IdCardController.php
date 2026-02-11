<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use PDF; // We will use DOMPDF for better print layout if needed, or just HTML print

class IdCardController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    // QR Content: Simple NIP or User ID for now. 
    // In a real scenario, this could be an encrypted string.
    $qrContent = $user->nip ?? $user->id;

    // Generate QR Code
    $qrCode = QrCode::size(200)->generate($qrContent);

    return view('teacher.id_card.index', compact('user', 'qrCode'));
  }
}
