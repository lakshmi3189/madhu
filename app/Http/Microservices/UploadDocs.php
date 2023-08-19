<?php

namespace App\Microservices;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

/*=================================================== Upload Docs=====================================================
Created By : Lakshmi kumari 
Created On : 02-May-2023 
Code Status : Open 
Description: Taken this code from juidco team
*/

// class DocUpload
// {
//     /**
//      * | Image Document Upload
//      * | @param refImageName format Image Name like SAF-geotagging-id (Pass Your Ref Image Name Here)
//      * | @param requested image (pass your request image here)
//      * | @param relativePath Image Relative Path (pass your relative path of the image to be save here)
//      * | @return imageName imagename to save (Final Image Name with time and extension)
//      */
//     public function upload($refImageName, $image, $relativePath)
//     {
//         $extention = $image->getClientOriginalExtension();
//         $imageName = time() . '-' . $refImageName . '.' . $extention;
//         $imageSize = $image->getSize();
//         $humanReadableSize = $imageSize / (1024 * 1024);

//         if ($extention != 'pdf' && $humanReadableSize > 1) {
//             $image = Image::make($image->path());
//             $image->resize(1024, 1024, function ($constraint) {
//                 $constraint->aspectRatio();
//             })->save($relativePath . '/' . $imageName);
//         } else
//             $image->move($relativePath, $imageName);

//         return $imageName;
//     }
// }


// public function upload($refImageName, $image, $relativePath)
//       {
//           $extention = $image->getClientOriginalExtension();
//           $imageName = time() . '-' . $refImageName . '.' . $extention;
//           $imageSize = $image->getSize();
//           $humanReadableSize = $imageSize / (1024 * 1024);

//           if ($extention != 'pdf' && $humanReadableSize > 1) {
//               $image = Image::make($image->path());
//               $image->resize(1024, 1024, function ($constraint) {
//                   $constraint->aspectRatio();
//               })->save($relativePath . '/' . $imageName);
//           } else
//               $image->move($relativePath, $imageName);

//           return $imageName;
//       }