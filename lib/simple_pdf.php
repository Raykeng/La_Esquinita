<?php
/**
 * Generador de PDF simple sin librerías externas
 * Usa la extensión GD de PHP para crear un PDF básico
 */

class SimplePDF {
    private $content = '';
    private $width = 595; // A4 width in points
    private $height = 842; // A4 height in points
    
    public function __construct() {
        $this->content = "%PDF-1.4\n";
    }
    
    public function addText($text, $x = 50, $y = 800, $size = 12) {
        // Simplified PDF text addition
        $this->content .= "BT\n";
        $this->content .= "/{$size} Tf\n";
        $this->content .= "{$x} {$y} Td\n";
        $this->content .= "({$text}) Tj\n";
        $this->content .= "ET\n";
    }
    
    public function output($filename = 'document.pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Basic PDF structure
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->width} {$this->height}] /Contents 4 0 R >>\nendobj\n";
        $pdf .= "4 0 obj\n<< /Length " . strlen($this->content) . " >>\nstream\n";
        $pdf .= $this->content;
        $pdf .= "\nendstream\nendobj\n";
        $pdf .= "xref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000225 00000 n \n";
        $pdf .= "trailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n" . (strlen($pdf) + 30) . "\n%%EOF";
        
        echo $pdf;
    }
}
?>