<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-plugin/wysiwyg-editor/qa-wysiwyg-editor.php
	Description: Editor module class for WYSIWYG editor plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/


class qa_fast_editor
{
	private $urltoroot;

	public function load_module($directory, $urltoroot)
	{
		$this->urltoroot = $urltoroot;
	}

	public function option_default($option)
	{
		if ($option == 'pell_cdn') {
			return false;
		}
	}

	public function admin_form(&$qa_content)
	{
		$saved = false;

		if (qa_clicked('pell_save_button')) {
			qa_opt('pell_cdn', (bool)qa_post_text('pell_cdn_field'));
			$saved = true;
		}


		return array(
			'ok' => $saved ? 'Editor settings saved' : null,

			'fields' => array(
				array(
					'label' => 'Use a CDN to load static content(CSS & JS files)',
					'type' => 'checkbox',
					'value' => (bool)qa_opt('pell_cdn'),
					'tags' => 'name="pell_cdn_field" id="pell_cdn_field"',
				),
			),

			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="pell_save_button"',
				),
			),
		);
	}

	public function calc_quality($content, $format)
	{
		return $format == 'html' ? 1.0 : 0.8;
	}

	public function get_field(&$qa_content, $content, $format, $fieldname, $rows)
	{
		$text = $content;
		$html = qa_html($content, true);

		if(qa_opt('pell_cdn')){
			$scriptsrc = 'https://cdnjs.cloudflare.com/ajax/libs/pell/0.7.0/pell.min.js?'.QA_VERSION;
			$css_src = 'https://cdnjs.cloudflare.com/ajax/libs/pell/0.7.0/pell.min.css';
		}else{
			$scriptsrc = $this->urltoroot.'static/pell.min.js?'.QA_VERSION;
			$css_src = $this->urltoroot.'static/pell.min.css?'.QA_VERSION;
		}

		$alreadyadded = false;

		if (isset($qa_content['script_src'])) {
			foreach ($qa_content['script_src'] as $testscriptsrc) {
				if ($testscriptsrc == $scriptsrc)
					$alreadyadded = true;
			}
		}
		if (!$alreadyadded) {
			$qa_content['script_src'][] = $scriptsrc;
			$qa_content['css_src'][] = $css_src;
		}

		return array(
			'tags' => 'name="'.$fieldname.'" id="'.$fieldname.'" class="pell_data" style="display: none;"',
			'value' => qa_html($text),
			'rows' => $rows,
			'html_prefix' => '<div  id="'.$fieldname.'_pell" class="pell"></div>',
		);
	}

	public function load_script($fieldname)
	{
		return 
			"$('body').on('click', '.pell-button', function(event) {event.preventDefault();return false;});".
			"var editor = window.pell.init({".
			"  element: document.getElementById('" . $fieldname.'_pell' . "'),".
			"  styleWithCSS: false,".
			"  actions: ['bold','underline','italic','strikethrough','heading1','heading2','paragraph','olist','ulist','code','line','link','image',],".
			"  onChange: function (html) {".
			"    document.getElementById('" . $fieldname . "').innerHTML = html".
			"  }".
      		"});".
			"editor.content.innerHTML = $('#" . $fieldname . "').text();";
	}
    
	public function focus_script($fieldname)
	{
		return "$('.pell-content').focus();";
	}

	public function update_script($fieldname)
	{
	}

	public function read_post($fieldname)
	{
		global $qa_sanitize_html_newwindow;
		$html = @qa_post_text($fieldname);
		return array(
			'format' => 'html',
			'content' => qa_sanitize_html($html, $qa_sanitize_html_newwindow),
		);
	}

}
