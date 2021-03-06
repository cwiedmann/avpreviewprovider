<?php

namespace OCA\AvPreviewProvider\getID3;

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.misc.exe.php                                         //
// module for analyzing EXE files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class exe extends handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$EXEheader = $this->fread(28);

		$magic = 'MZ';
		if (substr($EXEheader, 0, 2) != $magic) {
			$this->error('Expecting "'.lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.lib::PrintHexBytes(substr($EXEheader, 0, 2)).'"');
			return false;
		}

		$info['fileformat'] = 'exe';
		$info['exe']['mz']['magic'] = 'MZ';

		$info['exe']['mz']['raw']['last_page_size']          = lib::LittleEndian2Int(substr($EXEheader,  2, 2));
		$info['exe']['mz']['raw']['page_count']              = lib::LittleEndian2Int(substr($EXEheader,  4, 2));
		$info['exe']['mz']['raw']['relocation_count']        = lib::LittleEndian2Int(substr($EXEheader,  6, 2));
		$info['exe']['mz']['raw']['header_paragraphs']       = lib::LittleEndian2Int(substr($EXEheader,  8, 2));
		$info['exe']['mz']['raw']['min_memory_paragraphs']   = lib::LittleEndian2Int(substr($EXEheader, 10, 2));
		$info['exe']['mz']['raw']['max_memory_paragraphs']   = lib::LittleEndian2Int(substr($EXEheader, 12, 2));
		$info['exe']['mz']['raw']['initial_ss']              = lib::LittleEndian2Int(substr($EXEheader, 14, 2));
		$info['exe']['mz']['raw']['initial_sp']              = lib::LittleEndian2Int(substr($EXEheader, 16, 2));
		$info['exe']['mz']['raw']['checksum']                = lib::LittleEndian2Int(substr($EXEheader, 18, 2));
		$info['exe']['mz']['raw']['cs_ip']                   = lib::LittleEndian2Int(substr($EXEheader, 20, 4));
		$info['exe']['mz']['raw']['relocation_table_offset'] = lib::LittleEndian2Int(substr($EXEheader, 24, 2));
		$info['exe']['mz']['raw']['overlay_number']          = lib::LittleEndian2Int(substr($EXEheader, 26, 2));

		$info['exe']['mz']['byte_size']          = (($info['exe']['mz']['raw']['page_count'] - 1)) * 512 + $info['exe']['mz']['raw']['last_page_size'];
		$info['exe']['mz']['header_size']        = $info['exe']['mz']['raw']['header_paragraphs'] * 16;
		$info['exe']['mz']['memory_minimum']     = $info['exe']['mz']['raw']['min_memory_paragraphs'] * 16;
		$info['exe']['mz']['memory_recommended'] = $info['exe']['mz']['raw']['max_memory_paragraphs'] * 16;

$this->error('EXE parsing not enabled in this version of getID3() ['.$this->getid3->version().']');
return false;

	}

}
