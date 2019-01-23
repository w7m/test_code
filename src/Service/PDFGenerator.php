<?php
/**
 * Created by PhpStorm.
 * User: mabidi
 * Date: 14/01/2019
 * Time: 13:32
 */

namespace App\Service;


use App\Entity\Expert;
use App\Entity\Vehicle;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig_Environment;

class PDFGenerator
{

    /**
     * @var HttpKernelInterface
     */
    private $kernel;
    /**
     * @var \Twig_Environment
     */
    private $templating;


    /**
     * PDFGenerator constructor.
     */
    public function __construct(Twig_Environment $templating)
    {


        $this->templating = $templating;
    }

    public function generatePdf(Vehicle $vehicle, Expert $expert,HttpKernelInterface $kernel)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->templating->render('insurance/sinister/receptionist/mypdf.html.twig', [
            'title' => "Ordre de mission", 'vehicle' => $vehicle
            , 'expert' => $expert
        ]);


        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        $dompdf->stream("ordre de Mission", [
            "Attachment" => false
        ]);
        // In this case, we want to write the file in the public directory
        $publicDirectory = 'C:/xampp/htdocs/assurance-sinistres/public/ordreMission';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . '/OM_'.$vehicle->getRegistrationNumber().'_'.date("j, n, Y").'.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

    }
}