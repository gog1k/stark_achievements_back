<?php

namespace Database\Seeders;

use App\Models\DefaultRoomItem;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class DefaultRoomItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DefaultRoomItem::truncate();

        $items = [
            'sky',
            'grass',
            'room',
            'shelf',
            'window',
            'door',
        ];

        foreach ($items as $item) {
            $obj = $this->uploadFile($item, 'obj');
            $material = $this->uploadFile($item, 'mtl');
            $template = $this->uploadFile($item, 'jpg');

            DefaultRoomItem::create([
                'code' => $item,
                'object' => asset('storage/' . $obj),
                'material' => asset('storage/' . $material),
                'template' => asset('storage/' . $template),
            ]);
        }
    }

    /**
     * save base64 string to file
     *
     * @param $file
     * @param $type
     * @return string
     */
    public function uploadFile($file, $type): string
    {
        $file = UploadedFile::fake()->createWithContent(
            $type . '/' . $file . '.' . $type,
            file_get_contents(__DIR__ . '/defaultRoomItems/' . $file . '.' . $type)
        );

        return $file->store($type);
    }
}
