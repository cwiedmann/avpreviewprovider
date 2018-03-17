<?php
/**
 * Copyright (c) 2018, Carsten Wiedmann <carsten_sttgt@gmx.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author     Carsten Wiedmann <carsten_sttgt@gmx.de>
 * @copyright  Copyright (c) 2018, Carsten Wiedmann <carsten_sttgt@gmx.de>
 * @license    FreeBSD
 * 
 */

namespace OCA\AvPreviewProvider\Preview;

use OCP\Preview\IProvider;
use OCA\AvPreviewProvider\getID3 as AvPreviewProviderGetID3;

/**
 * {@inheritDoc}
 */
class AvProvider implements IProvider {
	/**
	 * {@inheritDoc}
	 */
	public function __construct() {
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/(audio|video)\/.+/';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAvailable(\OCP\Files\FileInfo $file) {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getThumbnail($path, $maxX, $maxY, $scalingup, $fileview) {
		// Initialize getID3 engine
		try {
			ob_start();
			$getID3 = new AvPreviewProviderGetID3\GetID3;
		} catch (\Exception $e) {
			\OC::$server->getLogger()->error(
				'getID3 says: ' . str_replace("\n", '; ', trim($e->getMessage())),
				array('app' => 'avpreviewprovider')
			);
			ob_end_clean();
			return false;
		}
		ob_end_clean();

		// set some options for getID3
		$getID3->setOption(Array(
			'option_tag_lyrics3' => false,  // don't need this
			'option_tags_html' => false,    // don't need this
			'option_extra_info' => false,   // don't need this
			'encoding' => 'bin',            // needed for asf files
		));

		// Analyze file and store returned data in $tags
		$tmpPath = $fileview->toTmpFile($path);
		$tags = $getID3->analyze($tmpPath);
		unlink($tmpPath);
		if (isset($tags['error'])) {
			\OC::$server->getLogger()->error(
				'getID3 says: ' . implode('; ', $tags['error']),
				array('app' => 'avpreviewprovider')
			);
			return false;
		}

		// Copies data from all subarrays of [tags] into [comments]
		AvPreviewProviderGetID3\lib::CopyTagsToComments($tags);
		if (!isset($tags['comments']['picture'][0]['data'])) {
			// no picture available
			return false;
		}

		// find the correct picture
		$picture = null;
		foreach ($tags['comments']['picture'] as $value) {
			if (isset($value['picturetype']) && ('Cover (front)' === $value['picturetype'])) {
				// e.g. MP3
				$picture = $value['data'];
				break;
			} elseif (isset($value['filename']) && ('cover.jpg' === $value['filename'])) {
				// e.g. MKV
				$picture = $value['data'];
				break;
			}
		}
		if (null === $picture) {
			// just use the first picture
			$picture = $tags['comments']['picture'][0]['data'];
		}

		// new image object
		if('' !== $picture) {
			$image = new \OC_Image();
			$image->loadFromData($picture);
			if ($image->valid()) {
				$image->scaleDownToFit($maxX, $maxY);

				return $image;
			}
		}

		return false;
	}
}
