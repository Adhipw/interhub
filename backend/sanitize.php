<?php
$internships = App\Models\Internship::all();
foreach ($internships as $i) {
    // Basic replacements
    $desc = preg_replace('/<li>\s*<p>/', '- ', $i->description);
    $desc = str_replace('<li>', '- ', $desc);
    $req = str_replace('<li>', '- ', $i->requirements);
    $ben = str_replace('<li>', '- ', $i->benefits);
    
    $desc = str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $desc);
    $req = str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $req);
    $ben = str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $ben);
    
    // Strip tags and decode entities
    $desc = trim(html_entity_decode(strip_tags($desc)));
    $req = trim(html_entity_decode(strip_tags($req)));
    $ben = trim(html_entity_decode(strip_tags($ben)));
    
    // Clean up multiple newlines
    $desc = preg_replace('/(\r?\n){2,}/', "\n\n", $desc);
    $req = preg_replace('/(\r?\n){2,}/', "\n\n", $req);
    $ben = preg_replace('/(\r?\n){2,}/', "\n\n", $ben);

    $i->description = $desc;
    $i->requirements = $req;
    $i->benefits = $ben;
    $i->save();
}
echo "Sanitization complete.\n";
