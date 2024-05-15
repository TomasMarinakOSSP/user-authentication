<?php

namespace App\Module\Admin\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostFacade;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    private PostFacade $facade;

    public function __construct(PostFacade $facade)
    {
        $this->facade = $facade;
    }

    public function renderShow(int $postId): void
    {
        $post = $this->facade->getPostById($postId);
    
        if (!$post) {
            $this->error('Stránka nebyla nalezena');
        }
    
        $this->facade->addView($postId); // Zde voláme metodu addView pro zvýšení počtu zhlédnutí
    
        $this->template->post = $post;
        $this->template->comments = $this->facade->getComments($postId);
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno:')
            ->setRequired();

        $form->addEmail('email', 'E-mail:');

        $form->addTextArea('content', 'Komentář:')
            ->setRequired();

        $form->addSubmit('send', 'Publikovat komentář');

        $form->onSuccess[] = [$this, 'commentFormSucceeded'];

        return $form;
    }

    public function commentFormSucceeded(Form $form, \stdClass $data): void
    {
        $postId = $this->getParameter('postId');

        $this->facade->createComment($postId, $data);

        $this->flashMessage('Děkuji za komentář', 'success');
        $this->redirect('this');
    }

    public function actionShow(int $postId): void
    {
        $post = $this->facade->getPostById($postId);
        if ($post && $post->status === 'ARCHIVED' && !$this->getuser()->isLoggedIn()) {
            $this->flashmessage('Nemáš právo vidět archivovaný příspěveky, kamarádíčku!');
            $this->redirect('Home:default');
        }

        $this->template->post = $post;
        $this->template->comments = $this->facade->getComments($postId);

    }
}
