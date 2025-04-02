<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;


class DownloadAndSaveImage
{
    public string $url;
    public mixed $fileName;
    public string $path;
    public int $width;
    public int $height;

    /**
     * Создаем экземпляр задачи.
     */
    public function __construct(string $url, $fileName = null, string $path = 'images', int $width = 100, int $height = 100)
    {
        $this->url = $url;
        $this->fileName = $fileName;
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;
    }

    public function run()
    {
        try {
            // 1. Загружаем изображение
            $response = Http::withOptions(['verify' => false])->get($this->url);

            if (!$response->successful()) {
                throw new \Exception("Ошибка загрузки изображения: {$this->url}");
            }

            $imageContent = $response->body();

            // 2. Проверяем, является ли файл изображением
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $imageContent);
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                throw new \Exception("Файл не является изображением: {$this->url}");
            }

            // 3. Создаем менеджер изображений
            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageContent);

            // 4. Делаем ресайз с сохранением пропорций
            $image = $image->resize($this->width, $this->height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Предотвращает увеличение маленьких изображений
            });

            // 5. Генерируем уникальное имя и сохраняем в формате WebP
            if ($this->fileName) {
                $fileName = $this->fileName . '.webp';
            } else {
                $fileName = uniqid() . '.webp';
            }
            $filePath = "{$this->path}/{$fileName}";

            Storage::disk('public')->put($filePath, $image->encode(new WebpEncoder(quality: 90)));

            Log::info("Изображение успешно сохранено: " . Storage::url($filePath));

        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение Job
            Log::error("Ошибка обработки изображения: " . $e->getMessage());
        }
    }
}
