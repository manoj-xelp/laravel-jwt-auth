<?php

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

if (!function_exists('generateToken')) {
    // Generates random token
    function generateToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}

// Appends base url to given uri
if (!function_exists('getUrl')) {
    function getUrl($url)
    {
        $base_url = url('/');
        if (App::environment() != "local") {
            $base_url = env('SERVER_URL');
        }
        return  $base_url . $url;
    }
}

if (!function_exists('fileUpload')) {
    function fileUpload($type, $file, $path)
    {
        try{
            $filename = strtolower($type).'_'.date('mdYHis').'_'.bin2hex(random_bytes(4)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            return $filename;
        } catch (\Exception $e) {
            Log::error($e);
            return -1;
        } 
    }
}

if(!function_exists('getFile')){
    function getFile($city_url,$value)
    {
        $url=env('SERVER_URL');
        $image_url=$url.$city_url.$value;
        return $image_url;
    }
}

if (!function_exists('uploadBase64Image')) {
    function uploadBase64Image($image, $path, $name)
    {
        $image = $image;
        $image_type = explode(';', explode('/', explode(',', $image)[0])[1])[0];
        $image = str_replace('data:image/' . $image_type . ';base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $name . time() . '.' . $image_type;
        file_put_contents(public_path() . $path . $imageName, base64_decode($image));
        return $imageName;
    }
}

if (!function_exists('manualPaginate')) {
    
    function manualPaginate($items, $perPage = 10, $page = 1, $options = []) {
        
        try {
            
            $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
            
            $items = $items instanceof Collection ? $items : Collection::make($items);
            
            $lap = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
            $lap = $lap->withPath(LengthAwarePaginator::resolveCurrentPath());
            return [
                'current_page' => $lap->currentPage(),
                'data' => $lap->values(),
                'first_page_url' => $lap->url(1),
                'from' => $lap->firstItem(),
                'last_page' => $lap->lastPage(),
                'last_page_url' => $lap->url($lap->lastPage()),
                'next_page_url' => $lap->nextPageUrl(),
                'per_page' => $lap->perPage(),
                'prev_page_url' => $lap->previousPageUrl(),
                'to' => $lap->lastItem(),
                'total' => $lap->total(),
            ];
            
        } catch (\Exception $e) {
            Log::error($e);
            return $e->getMessage();
        }
    }
}