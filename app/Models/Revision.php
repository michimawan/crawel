<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Revision extends Model
{
    protected $fillable = [
        'child_tag_revisions',
        'time_to_check_story',
        'end_time_check_story',
        'end_time_run_automate_test',
        'time_get_canary',
        'time_to_finish_test_canary',
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
        return $this->belongsToMany(Tag::class, 'tag_revision', 'revision_id', 'tag_id');
    }

    /**
     * method section
     */
    public function syncTags($tagIds = []) {
        if (is_null($tagIds) || count($tagIds) == 0) {
            return;
        }
        $tagIds = array_filter($tagIds, function($c){
            return !empty($c);
        });
        $this->tags()->sync($tagIds);

        $this->cached_tags = $this->tags()->get();
        $this->save();
    }
}
