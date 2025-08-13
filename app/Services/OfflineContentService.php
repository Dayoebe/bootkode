<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class OfflineContentService
{
    // public function downloadCourseForUser($course, $user)
    // {
    //     $path = $this->getUserCoursePath($user, $course);
        
    //     if (!Storage::exists($path)) {
    //         Storage::makeDirectory($path);
    //     }
        
    //     // Download lessons
    //     foreach ($course->lessons as $lesson) {
    //         $this->downloadLesson($lesson, $path);
    //     }
        
    //     // Download PDFs
    //     foreach ($course->pdfResources as $pdf) {
    //         $this->downloadPdf($pdf, $path);
    //     }
        
    //     return $path;
    // }
    
    protected function downloadLesson($lesson, $path)
    {
        // Implementation depends on your lesson storage
    }
    
    protected function downloadPdf($pdf, $path)
    {
        // Implementation depends on your PDF storage
    }
    
    public function getUserCoursePath(User $user, $course)
    {
        return config('app.offline_content_path') . "/user_{$user->id}/course_{$course->id}";
    }
    
    public function getUserOfflineContentSize(User $user)
    {
        $path = config('app.offline_content_path') . "/user_{$user->id}";
        
        if (!Storage::exists($path)) {
            return 0;
        }
        
        $size = 0;
        foreach (Storage::allFiles($path) as $file) {
            $size += Storage::size($file);
        }
        
        return round($size / 1024 / 1024, 2); // Convert to MB
    }

    // In OfflineContentService
public function downloadCourseForUser($course, $user)
{
    $path = $this->getUserCoursePath($user, $course);
    
    if (!Storage::exists($path)) {
        Storage::makeDirectory($path);
    }

    // Download lessons
    foreach ($course->lessons as $lesson) {
        $this->downloadLesson($lesson, $path);
    }

    // Download other resources
    foreach ($course->pdfResources as $pdf) {
        $this->downloadPdf($pdf, $path);
    }

    return $path;
}
}