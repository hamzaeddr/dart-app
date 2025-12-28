<?php

namespace App\Jobs;

use App\Models\Contribution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StampReceiptPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $contributionId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $contributionId)
    {
        $this->contributionId = $contributionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contribution = Contribution::with(['daret', 'user', 'confirmedBy'])
            ->find($this->contributionId);

        if (! $contribution) {
            return;
        }

        $receiptMedia = $contribution->getFirstMedia('receipt');

        if (! $receiptMedia) {
            return;
        }

        $html = view('pdf.receipt-stamp', [
            'contribution' => $contribution,
            'daret' => $contribution->daret,
            'user' => $contribution->user,
            'confirmedBy' => $contribution->confirmedBy,
            'receiptPath' => $receiptMedia->getPath(),
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        $contribution->clearMediaCollection('stamped_receipt');
        $contribution
            ->addMediaFromString($pdf->output())
            ->usingFileName('receipt-stamped-'.$contribution->id.'.pdf')
            ->toMediaCollection('stamped_receipt');
    }
}
