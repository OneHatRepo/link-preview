<?php

namespace Dusterio\LinkPreview\Parsers;

use Dusterio\LinkPreview\Contracts\LinkInterface;
use Dusterio\LinkPreview\Contracts\ReaderInterface;
use Dusterio\LinkPreview\Contracts\ParserInterface;
use Dusterio\LinkPreview\Contracts\PreviewInterface;
use Dusterio\LinkPreview\Models\VideoPreview;
use Dusterio\LinkPreview\Readers\HttpReader;

class RumbleParser extends BaseParser implements ParserInterface {
	
	const PATTERN = '/^.*(?:rumble.com)\\/([^\-]+)\-([\w]+)(?:$|\\/|\\?)/';

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
		preg_match(static::PATTERN, $link->getUrl(), $matches);

		$this->getPreview()
			->setId($matches[1])
			->setEmbed(
				'<iframe class="rumble" width="640" height="360" src="https://rumble.com/embed/'.$this->getPreview()->getId().'/?pub=7olpn" frameborder="0" allowfullscreen></iframe>'
			);

		return $this;
	}
}
