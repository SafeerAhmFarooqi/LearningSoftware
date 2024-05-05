<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use voku\helper\HtmlDomParser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.application.index');
});

Route::get('/date-range-picker2', function () {
    return view('pages.application.date-range-picker2');
});

Route::get('/date-range-picker', function () {
    return view('pages.application.date-range-picker');
});

Route::get('/fesco-bill', function () {
    $response = Http::get("https://bill.pitc.com.pk/fescobill/general?refno=10131730499800");
    //$html = ;
    $cleanedHtml = cleanHTML($response->body());
    $cleanedHtml = str_replace(["\n", "\r"], '', $cleanedHtml);
    //dd($cleanedHtml);
    $htmlParser = HtmlDomParser::str_get_html($response->body());
    $presentReadingDate = $htmlParser->find('table table tr',4)->find('td',2)->innertext;
    $presentReadingDate = strip_tags(html_entity_decode($presentReadingDate));
    $presentReadingDate = (int)preg_replace('/[^0-9]/', '', $presentReadingDate);
    dd($presentReadingDate);
    dd(HtmlDomParser::str_get_html($cleanedHtml));
    //dd($html);
    
    return view('pages.application.fesco-bill');
    //return view('pages.application.shooting-game');
    //return view('pages.application.shooting-game2');
});

Route::get('/json-response', function () {
    return dd('500 returened');
    return collect([
        'id' => 1,
        'name' => 'Safeer',
        'age' => 35,
    ])->toJson();
})->name('json.response');

function cleanHTML($html) {
    // Create a new DOMDocument
    $dom = new DOMDocument;
    
    // Load the HTML content, suppressing errors
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Create a new DOMXPath instance
    $xpath = new DOMXPath($dom);

    // Remove unwanted elements or attributes
    // Example: Remove all script tags
    $scripts = $xpath->query('//script');
    foreach ($scripts as $script) {
        $script->parentNode->removeChild($script);
    }

    // Normalize the document
    $dom->normalizeDocument();

    // Save the cleaned HTML
    $cleanedHtml = $dom->saveHTML();

    return $cleanedHtml;
}
