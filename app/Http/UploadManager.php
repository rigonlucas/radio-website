<?php

namespace App\Http;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
//use Illuminate\Support\Facades\File;

class UploadManager
{
    protected $user;

    /*
     * Armazena um avatar
     * */
    public static function storeAvatar($user, $avatar) {
        $oldAvatar = $user->avatar;
        if($oldAvatar != null){
            Storage::delete($oldAvatar);
        }


        $fileName = $user->id . "." . $avatar->getClientOriginalExtension();
        $img = Image::make($avatar->getLinkTarget())->resize(800, 800);
        $img->save(storage_path('app/public/avatars/' . $fileName));
        //$avatar->storeAs('/avatars', $fileName);

        $path = '/storage/avatars/' . $fileName;
        return $path;
    }

    public static function storeThumbnail($post, $thumbnail) {
        $oldThumb = $post->thumbnail;
        if($oldThumb != null){
            Storage::delete($oldThumb);
        }

        $img = Image::make($thumbnail->getLinkTarget());
        $fileName = $post->id . "." . $thumbnail->getClientOriginalExtension();

        $img->save(storage_path('app/public/thumbnails/' . $fileName));

        $path = '/storage/thumbnails/' . $fileName;
        return $path;
    }

    public static function storeSupporterImg($supporter, $img) {
        $oldImg = $supporter->image;
        if($oldImg != null){
            Storage::delete($oldImg);
        }

        $fileName = $supporter->id . "." . $img->getClientOriginalExtension();
        $img = Image::make($img);
        $img->save(storage_path('app/public/supporters/' . $fileName));

        $path = '/storage/supporters/' . $fileName;
        return $path;
    }

    public static function deleteFile($filePath) {
        return Storage::delete($filePath);
    }

    public static function storeTempImg($img, $key) {
        //dd($img);
        $fileName = $img->getClientOriginalName();

        $img->storeAs('gallery/temp/' . $key, $fileName);

        $path = '/storage/gallery/' . $fileName;
        return $path;
    }

    public static function destroyTempImg($img, $key) {
        //dd($img);
        return Storage::delete('gallery/temp/' . $key . '/' . $img);
    }

    /**
     * Recupera imagens da pasta temp
     *
     * @param $post id do post
     * @param $key key da galeria
     * @param $imgs string com o nome das imagens separados por uma vírgula
     * @return \Illuminate\Http\Response
     */
    public static function recoveryImg($post, $key, $imgs) {
        $imgs = explode(',', $imgs);
        if(!Storage::exists('gallery/temp/' . $key)){
            return false;
        }
        Storage::move('gallery/temp/' . $key, 'gallery/stored/' . $post);
        return Storage::allFiles('gallery/stored/' . $post);
    }
}
