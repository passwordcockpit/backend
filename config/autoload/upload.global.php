<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

$availableMimeTypes=require 'mimetypes.php';

$acceptedMimeTypes=[
    'application/pdf' => 'pdf'
];
if(getenv('PASSWORDCOCKPIT_UPLOAD_ACCEPTED_MIMETYPES')){
    $desiredMimeTypes=explode(',',getenv('PASSWORDCOCKPIT_UPLOAD_ACCEPTED_MIMETYPES'));
    $acceptedMimeTypes=[];
    foreach($desiredMimeTypes as $desiredMimeType){
        $desiredMimeType=trim($desiredMimeType);
        if(isset($availableMimeTypes[$desiredMimeType])){
            $acceptedMimeTypes[$availableMimeTypes[$desiredMimeType]]=$desiredMimeType;
        }
    }
}
return [
    'upload_config' => [
        'upload_path' => 'upload',
        'accepted_mime_types' => $acceptedMimeTypes
    ]
];
