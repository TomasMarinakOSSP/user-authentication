<?php

namespace App\Module\Admin\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostFacade;

final class EditPresenter extends Nette\Application\UI\Presenter
{
    private PostFacade $postFacade;

    public function __construct(PostFacade $postFacade)
    {
        $this->postFacade = $postFacade;
    }

    protected function createComponentPostForm(): Form
    {
    $form = new Form;
    $form->addText('title', 'Titulek:')
    ->setRequired();
    $form->addTextArea('content', 'Obsah:')
    ->setRequired();
    $form->addUpload('image', 'Soubor')
    ->setRequired()
    ->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG, or GIF');
    $statuses = [
                'OPEN' => 'OTEVŘENÝ',
                'CLOSED' => 'UZAVŘENÝ',
                'ARCHIVED' => 'ARCHIVOVANÝ'
            ];
            $form->addSelect('status', 'Stav:', $statuses)
                ->setDefaultValue('OPEN');
    
    $form->addSubmit('send', 'Uložit a publikovat');
    $form->onSuccess[] = $this->postFormSucceeded(...);
    
    return $form;
    }
    
    // Change private to protected or public
    protected function postFormSucceeded(array $data): void
    {
        $postId = $this->getParameter('postId');

        // Handle file upload
        $image = $data['image'];
        if ($image->isOk()) {
            $imageName = $image->getSanitizedName();
            $image->move('upload/' . $imageName);
            $data['image'] = 'upload/' . $imageName;
        } else {
            $this->flashMessage('Soubor nebyl přidán', 'failed');
        }

        // Update or insert post
        if ($postId) {
            $post = $this->postFacade->editPost($postId, $data);
        } else {
            $post = $this->postFacade->insertPost($data);
        }

        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('Post:show', $post->id);
    }

    public function renderEdit(int $postId): void
    {
        $post = $this->postFacade->getPostById($postId);

        if (!$post) {
            $this->error('Post not found');
        }

        $this->getComponent('postForm')->setDefaults($post->toArray());
        $this->template->post = $post;
        
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    public function handleDeleteImage(int $postId) {
        $data['image'] = null;
        $this->postFacade->editPost($postId, $data);
        $this->flashMessage('Obrázek k příspěvku byl smazán');
    }
}
