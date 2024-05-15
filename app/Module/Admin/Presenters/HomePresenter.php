<?php
namespace App\Module\Admin\Presenters;

use App\Model\PostFacade;
use Nette;
use Nette\Utils\Paginator;
final class HomePresenter extends Nette\Application\UI\Presenter
{
	private const POSTS_PER_PAGE = 10;

	public function __construct(
		private PostFacade $facade,
	) {
	}

	public function renderDefault(int $page = 1): void // metoda pro zobrazení domovské stránky s články 
	{
		$paginator = new Paginator; // vytvoření instance paginatoru
		$paginator->setItemCount($this->facade->getPublicArticles()->count()); // celkový počet článků v databázi
		$paginator->setItemsPerPage(self::POSTS_PER_PAGE); // počet článků na stránku 
		$paginator->setPage($page); // aktuální stránka 
		$posts = $this->facade // získání článků pro aktuální stránku
			->getPublicArticles() // metoda pro získání veřejných článků
			->limit($paginator->getLength(), $paginator->getOffset()); // omezení počtu článků na aktuální stránku

		$this->template->posts = $posts; // předání článků do šablony
		$this->template->paginator = $paginator; // předání paginatoru do šablony
	}
}
