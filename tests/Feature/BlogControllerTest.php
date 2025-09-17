<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_display_the_create_blog_form_with_all_tags()
    {
        Tag::factory()->count(3)->create();
        $response = $this->get(route('blogs.create'));
        $response->assertStatus(200)
                 ->assertViewIs('blogs.create')
                 ->assertViewHas('tags')
                 ->assertViewHas('tags', function ($tags) {
                     return $tags->count() === 3;
                 });
    }

    #[Test]
    public function it_can_store_a_new_blog_with_selected_tags()
    {
        $tag1 = Tag::factory()->create(['name' => 'Laravel']);
        $tag2 = Tag::factory()->create(['name' => 'PHP']);
        $blogData = [
            'name' => 'Test Blog',
            'description' => 'This is a test blog description.',
            'tags' => [$tag1->id, $tag2->id]
        ];
        $response = $this->post(route('blogs.store'), $blogData);
        $response->assertStatus(302)
                 ->assertRedirect(route('blogs.index'))
                 ->assertSessionHas('success', 'Blog created!');
        $this->assertDatabaseHas('blogs', [
            'name' => 'Test Blog',
            'description' => 'This is a test blog description.'
        ]);
        $blog = Blog::where('name', 'Test Blog')->first();
        $this->assertCount(2, $blog->tags);
        $this->assertTrue($blog->tags->contains($tag1->id));
        $this->assertTrue($blog->tags->contains($tag2->id));
    }

    #[Test]
    public function it_validates_required_fields_when_storing_a_blog()
    {
        $response = $this->post(route('blogs.store'), [
            'description' => 'Description only.'
        ]);
        $response->assertSessionHasErrors(['name' => 'The name field is required.']);
        $response = $this->post(route('blogs.store'), [
            'name' => 'Valid Name',
            'description' => 'Valid Description',
            'tags' => []
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('blogs', ['name' => 'Valid Name']);
    }

    #[Test]
    public function it_can_display_a_list_of_all_blogs_with_their_tags()
    {
        $tag1 = Tag::factory()->create(['name' => 'Laravel']);
        $tag2 = Tag::factory()->create(['name' => 'Testing']);
        $blog1 = Blog::factory()->create(['name' => 'Blog 1']);
        $blog1->tags()->attach($tag1);
        $blog2 = Blog::factory()->create(['name' => 'Blog 2']);
        $blog2->tags()->attach([$tag1->id, $tag2->id]);
        $response = $this->get(route('blogs.index'));
        $response->assertStatus(200)
                 ->assertViewIs('blogs.index')
                 ->assertViewHas('blogs', function ($blogs) {
                     return $blogs->count() === 2 &&
                            $blogs->first()->tags->count() === 1 &&
                            $blogs->last()->tags->count() === 2;
                 });
    }

    #[Test]
    public function it_can_search_blogs_by_tag_name()
    {
        $laravelTag = Tag::factory()->create(['name' => 'Laravel']);
        $phpTag = Tag::factory()->create(['name' => 'PHP']);
        $blog1 = Blog::factory()->create(['name' => 'Laravel Blog']);
        $blog1->tags()->attach($laravelTag);
        $blog2 = Blog::factory()->create(['name' => 'PHP Blog']);
        $blog2->tags()->attach($phpTag);
        $blog3 = Blog::factory()->create(['name' => 'Other Blog']);
        $response = $this->get(route('blogs.search'), ['search' => 'Lar']);
        $response->assertStatus(200)
                 ->assertViewIs('blogs.index')
                 ->assertViewHas('blogs', function ($blogs) use ($blog1) {
                     return $blogs->count() === 1 && $blogs->first()->id === $blog1->id;
                 })
                 ->assertViewHas('searchQuery', 'Lar');
    }

    #[Test]
    public function it_returns_all_blogs_when_search_query_is_empty()
    {
        Blog::factory()->count(2)->create();
        $response = $this->get(route('blogs.search'), ['search' => '']);
        $response->assertViewHas('blogs', function ($blogs) {
            return $blogs->count() === 2;
        });
    }
}