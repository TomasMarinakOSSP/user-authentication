<?php

namespace App\Model;

use Nette;

final class PostFacade
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    public function getPublicArticles()
    {
        return $this->database
            ->table('posts')
            ->where('created_at < ', new \DateTime)
            ->order('created_at DESC');
    }

    public function getPostById(int $postId)
    {
        return $this->database
            ->table('posts')
            ->get($postId);
    }

    public function getComments(int $postId)
    {
        return $this->database
            ->table('comments')
            ->where('post_id', $postId)
            ->order('created_at');
    }

    public function addComment(int $postId, \stdClass $data)
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'name' => $data->name,
            'email' => $data->email,
            'content' => $data->content,
        ]);
    }

    public function createComment(int $postId, \stdClass $data)
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'name' => $data->name,
            'email' => $data->email,
            'content' => $data->content,
        ]);
    }

    public function editPost(int $postId, array $data)
    {
        $this->database
            ->table('posts')
            ->get($postId)
            ->update($data);
    }

    public function insertPost(array $data)
    {
        return $this->database
            ->table('posts')
            ->insert($data);
    }

  
    public function addView(int $postId): void
{
    $this->database->table('posts')
        ->where('id', $postId)
        ->update(['views' => new Nette\Database\SqlLiteral('views + 1')]);
}
}
