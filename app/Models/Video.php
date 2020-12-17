<?php

namespace App\Models;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe as FFMpegFFProbe;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as SupportFFMpeg;

class Video extends Model
{
    use HasFactory;

    /**
     * table
     *
     * @var string
     */
    protected $table = "video_log";

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'data'
    ];

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    public function getProperties(string $filename)
    {
        $ffmpeg = SupportFFMpeg::fromFilesystem(Storage::disk('local'))->open($filename);

        $dim = $ffmpeg->getVideoStream()->getDimensions();
        $in_width = $dim->getWidth();
        $in_height = $dim->getHeight();

        $ffprobe = FFMpegFFProbe::create()->streams(Storage::disk('local')->path($filename))->videos()->first();
        $framerate = $ffprobe->get('r_frame_rate');
        $bitrate = $ffprobe->get('bit_rate');
        $codec = $ffprobe->get('codec_name');

        return [
            'orientation' => ($in_width > $in_height) ? 'Horizontal' : 'Vertical',
            'resolution' => $in_width . 'x' . $in_height,
            'duration' => $ffmpeg->getDurationInSeconds(),
            'framerate' => $framerate,
            'bitrate' => $bitrate,
            'codec' => $codec,
        ];
    }

    /**
     * convert
     *
     * @param  string $filename
     * @param  int    $width
     * @param  int    $height
     * @param  array  $metadatas
     * @return void
     */
    public function convert(string $filename, int $width, int $height, array $metadatas)
    {
        $ffmpeg = SupportFFMpeg::fromFilesystem(Storage::disk('local'))->open($filename);

        dd($ffmpeg->getFFProbe()->videos()->first()->get('r_frame_rate'));

        $output = 'converted/' . uniqid() . '.mp4';

        $dim = $ffmpeg->getVideoStream()->getDimensions();
        $in_width = $dim->getWidth();
        $in_height = $dim->getHeight();

        $format = new X264('libmp3lame', 'libx264');
        $format->on('progress', function($video, $format, $percentage) {
            echo $percentage . '%';
        });

        foreach ($metadatas as $key => $metadata) $ffmpeg->addFilter('-metadata', $key . '=' . $metadata);

        $ffmpeg
            ->addFilter(function (VideoFilters $filters) use (&$in_width, &$in_height) {
                $filters->crop(new \FFMpeg\Coordinate\Point(((int) ($in_width / 3)), 0), new \FFMpeg\Coordinate\Dimension(((int) ($in_width / 3)), $in_height));
            })
            ->export()->inFormat($format)->save($output);

        SupportFFMpeg::fromFilesystem(Storage::disk('local'))->open($output)
            ->addFilter(function (VideoFilters $filters) use (&$width, &$height) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension($width, $height));
            })
            ->export()->inFormat(new X264('libmp3lame', 'libx264'))
            ->save('converted/' . uniqid() . '.mp4');

        Storage::delete($output);
    }
}
