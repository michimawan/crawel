<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Revision extends Model
{
    protected $fillable = [
        'child_tag_revisions',
        'end_time_check_story',
        'end_time_run_automate_test',
        'time_get_canary',
        'time_to_elb',
        'description',
        'project',
    ];

    public $cached_tags;

    /**
     * relation section
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * method section
     */
    public function syncTags($tagIds = []) {
        $this->unnatachTags();

        if (is_null($tagIds) || count($tagIds) == 0) {
            return;
        }
        $tagIds = array_filter($tagIds, function($c){
            return !empty($c);
        });

        $tags = Tag::whereIn('id', $tagIds)->get();
        $this->tags()->saveMany($tags);

        $this->cached_tags = $this->tags()->get();
    }

    private function unnatachTags()
    {
        $tags = $this->tags()->get();
        if (! $tags) {
            return;
        }
        $tagIds = $tags->pluck('id')->all();
        $tags = Tag::whereIn('id', $tagIds)->get();
        foreach ($tags as $tag) {
            $tag->revision_id = null;
            $tag->save();
        }
    }
}
