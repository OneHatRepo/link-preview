<?php

namespace Dusterio\LinkPreview\Parsers;

use Dusterio\LinkPreview\Contracts\LinkInterface;
use Dusterio\LinkPreview\Contracts\ReaderInterface;
use Dusterio\LinkPreview\Contracts\ParserInterface;
use Dusterio\LinkPreview\Contracts\PreviewInterface;
use Dusterio\LinkPreview\Models\VideoPreview;
use Dusterio\LinkPreview\Readers\HttpReader;

class RumbleParser extends BaseParser implements ParserInterface {
	
	const PATTERN = '/^.*(?:rumble.com)\\/([^\-]+)\-(.+)(?:$|\\/|\\?)/';

	/**
	 * @param ReaderInterface $reader
	 * @param PreviewInterface $preview
	 */
	public function __construct(ReaderInterface $reader = null, PreviewInterface $preview = null)
	{
		$this->setReader($reader ?: new HttpReader());
		$this->setPreview($preview ?: new VideoPreview());
	}

	/**
	 * @inheritdoc
	 */
	public function __toString() 
	{
		return 'rumble';
	}

	/**
	 * @inheritdoc
	 */
	public function canParseLink(LinkInterface $link)
	{
		return (preg_match(static::PATTERN, $link->getUrl()));
	}

	/**
	 * @inheritdoc
	 */
	public function parseLink(LinkInterface $link)
	{
		$link = $this->readLink($link);
		$html = $link->getContent();
		$regex = '/embedUrl":"([^"]+)"/'; // matches 'embedUrl":"https://rumble.com/embed/v97gkx/"'
		preg_match($regex, $html, $matches);
		if (!isset($matches[1]) || empty($matches[1])) {
			return;
		}
		$html = $matches[1];
		$regex = '/^.*(rumble.com)\/embed\/([^\/]+)/'; // matches 'https://rumble.com/embed/v97gkx/'
		preg_match($regex, $html, $matches);
		if (!isset($matches[2]) || empty($matches[2])) {
			return;
		}
		$id = $matches[2];
		
		$this->getPreview()
			->setId($id)
			->setEmbed(
				'<iframe class="rumble" width="640" height="360" src="https://rumble.com/embed/' . $id . '/?pub=7olpn" frameborder="0" allowfullscreen></iframe>'
			);

		return $this;
	}
}
