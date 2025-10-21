<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;

class PdfFooter
{
    /**
     * Add a stacked right-bottom footer to a DomPDF instance.
     *
     * @param \Barryvdh\DomPDF\PDF $pdf  The PDF instance returned by PDF::loadView()
     * @param array $opts Optional settings:
     *                    - title: string (default 'PERISAI')
     *                    - pagePrefix: string (default 'Hal')
     *                    - bottomPx: int (default 30)
     *                    - rightMm: int (default 10)
     *                    - titleSize: int (default 10)
     *                    - pageSize: int (default 9)
     *                    - font: string (default 'DejaVu Sans')
     *
     * @return void
     */
    public static function add_right_corner_footer($pdf, array $opts = [])
    {
        $title = $opts['title'] ?? 'PERISAI';
        $pagePrefix = $opts['pagePrefix'] ?? 'Hal';
        $bottomPx = $opts['bottomPx'] ?? 30; // px
        $rightMm = $opts['rightMm'] ?? 10; // mm
        $titleSize = $opts['titleSize'] ?? 10;
        $pageSize = $opts['pageSize'] ?? 9;
        $fontName = $opts['font'] ?? 'Helvetica';

        $dompdf = $pdf->getDomPDF();
        // ensure rendering so canvas is available
        $dompdf->render();
        $canvas = $dompdf->getCanvas();

        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($title, $pagePrefix, $bottomPx, $rightMm, $titleSize, $pageSize, $fontName) {
            $font = $fontMetrics->getFont($fontName, 'normal');

            // convert px -> pt (1px = 72/96 pt)
            $ptPerPx = 72 / 96;
            $bottomOffset = $bottomPx * $ptPerPx;

            // convert mm -> pt
            $ptPerMm = 72 / 25.4;
            $rightOffset = $rightMm * $ptPerMm;

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            $titleText = $title;
            $pageText = "$pagePrefix {$pageNumber} dari {$pageCount}";

            $titleWidth = $fontMetrics->getTextWidth($titleText, $font, $titleSize);
            $pageWidth = $fontMetrics->getTextWidth($pageText, $font, $pageSize);

            $xTitle = $w - $rightOffset - $titleWidth;
            $xPage = $w - $rightOffset - $pageWidth;

            $yPage = $h - $bottomOffset;
            $yTitle = $yPage - ($pageSize + 4);

            $canvas->text($xTitle, $yTitle, $titleText, $font, $titleSize);
            $canvas->text($xPage, $yPage, $pageText, $font, $pageSize);
        });
    }

    /**
     * Add default centered single-line footer: "PERISAI :: Hal X dari Y" at y = h - 30
     * Matches the style in the snippet: centered, 30pt from bottom (literal 30 used as in example).
     *
     * @param \Barryvdh\DomPDF\PDF $pdf
     * @param array $opts Optional: 'font' (default 'Helvetica'), 'size' (default 9)
     * @return void
     */
    public static function add_default($pdf, array $opts = [])
    {
        $fontName = $opts['font'] ?? 'Helvetica';
        $size = $opts['size'] ?? 9;

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();

        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($fontName, $size) {
            $text = "PERISAI :: Hal {$pageNumber} dari {$pageCount}";
            $font = $fontMetrics->getFont($fontName, 'normal');

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($w - $textWidth) / 2;
            $y = $h - 30; // 30pt from bottom (keeps literal 30 as in example)

            $canvas->text($x, $y, $text, $font, $size);
        });
    }
}
