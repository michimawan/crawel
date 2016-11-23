<?php

use App\Models\Tag;
use App\Models\Revision;

class RevisionTest extends BaseModelTest
{
    public function test_syncTags()
    {
        $tags = factory(Tag::class, 2)->create();
        $tagIds = $tags->pluck('id')->all();

        $revision = factory(Revision::class)->create();
        $this->assertEquals(0, $revision->tags()->count());

        $revision->syncTags($tagIds);

        $this->assertEquals(2, $revision->tags()->count());
        $this->assertEquals(2, $revision->cached_tags->count());
    }

    public function test_syncTags_when_has_some_tags_before()
    {
        $revision = factory(Revision::class)->create();
        $oldTags = factory(Tag::class, 2)->create([
            'revision_id' => $revision->id,
        ]);
        $oldTagIds = $oldTags->pluck('id')->all();

        $newTags = factory(Tag::class, 2)->create();
        $newTagIds = $newTags->pluck('id')->all();

        $this->assertEquals(2, $revision->tags()->count());

        $revision->syncTags($newTagIds);

        $this->assertEquals(2, $revision->tags()->count());
        $this->assertEquals(2, $revision->cached_tags->count());
        foreach ($newTagIds as $idx => $tagId) {
            $this->assertEquals($tagId, $revision->cached_tags->get($idx)->id);
        }
    }

    public function test_syncTags_when_revision_is_not_persist()
    {
        $revisionIds = [100000, 999999];

        $revision = factory(Revision::class)->create();

        $revision->syncTags($revisionIds);

        $this->assertEquals(0, $revision->tags->count());
        $this->assertEquals(0, $revision->cached_tags->count());
    }

    public function test_syncTags_when_revision_is_not_exist()
    {
        $revision = factory(Revision::class)->create();

        $revision->syncTags([null, null, '']);

        $this->assertEquals(0, $revision->tags->count());
        $this->assertEquals(0, $revision->cached_tags->count());
    }
}
