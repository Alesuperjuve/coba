<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ShenzenPDFController extends Controller
{
    public function index()
    {
        return view('shenzen.index');
    }

    public function extract(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:2048'
        ]);

        $file = $request->file('pdf_file');
        $filePath = $file->store('pdfs');

        $pdfPath = storage_path('app/' . $filePath);
        $credentialsPath = base_path('pdfservices-api-credentials.json');

        $process = new Process([
            "C:\\Program Files\\Python313\\python.EXE",
            base_path('extract.py'),
            $pdfPath,
            $credentialsPath
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            return back()->with('error', 'Ekstraksi gagal: ' . $process->getErrorOutput());
        }

        $output = json_decode($process->getOutput(), true);

        return view('shenzen.index', ['texts' => $output['texts']]);
    }





}
