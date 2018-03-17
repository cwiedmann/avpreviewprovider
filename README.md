# AV Preview Provider

Use getID3 to create audio/video thumbnails in Nextcloud.

## Description

This app attempts to create a thumbnail image from the embedded cover for all audio/video files using the getID3 library.

## Prerequisites
- Nextcloud >=13.0.1 <14.0.0

## Installing

### Install from source

- clone the repo in the apps folder
- activate the AV Preview Provider App on the apps page

Additionally, you can deactivate the core preview provider for MP3 in "config.php" by removing "OC\\Preview\\MP3" from the array.:

```
'enabledPreviewProviders' => array (  
	0 => 'OC\\Preview\\PNG',  
	1 => 'OC\\Preview\\JPEG',  
	2 => 'OC\\Preview\\GIF',  
	3 => 'OC\\Preview\\BMP',  
	4 => 'OC\\Preview\\XBitmap',  
	5 => 'OC\\Preview\\TXT',  
	6 => 'OC\\Preview\\MarkDown',  
),  
```

## Author

- **Carsten Wiedmann** [https://github.com/cwiedmann](https://github.com/cwiedmann)

## License

This project is licensed under the FreeBSD License - see the [license.txt](license.txt) file for details

## Acknowledgments

- Nextcloud / server [https://github.com/nextcloud/server](https://github.com/nextcloud/server)
- James Heinrich / getID3 [https://github.com/JamesHeinrich/getID3](https://github.com/JamesHeinrich/getID3)
- Hans-Peter Buniat / namespaceRefactor.php [https://gist.github.com/hpbuniat/3860059](https://gist.github.com/hpbuniat/3860059)
