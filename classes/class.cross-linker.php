<?php
Namespace WordPress\Plugin\Encyclopedia;

class Cross_Linker {
  private
    $DOM = False,
    $XPath = False,
    $skip_elements = Array(),
    $link_complete_words_only = False,
    $replace_phrases_once = False,
    $link_target = '_self',
    $escape_tags = Array('script', 'style', 'code', 'pre'), # These tags will not be loaded inside the PHP DOMDocument object
    $cache_expression = '{CACHE:%s}',
    $data_cache = Array();

  function loadContent($content){
    $encoded_content = MB_Convert_Encoding($content, 'HTML-ENTITIES', 'UTF-8');
    $encoded_content = $this->escapeTags($this->escape_tags, $encoded_content);

    $this->DOM = New \DOMDocument();
    If (!@$this->DOM->loadHTML($encoded_content)) return False; # Here we could get a Warning if the $content is not valid HTML
    $this->XPath = New \DOMXPath($this->DOM);
    return True;
  }

  function escapeTags($tags, $content){
    If (!Is_Array($tags)) return $content;
    ForEach ($tags As $tag){
      $regex = SPrintF('%%(<%1$s\b[^>]*>)(.*)(</%1$s>)%%imsuU', $tag);
      $content = PReg_Replace_Callback($regex, Array($this, 'cacheMatch'), $content);
    }
    return $content;
  }

  function cacheMatch($match){
    $string = $match[2];
    $key = MD5($string);
    $this->data_cache[$key] = $string;
    return $match[1] . SPrintF($this->cache_expression, $key) . $match[3];
  }

  function unCacheStrings($content){
    ForEach ($this->data_cache As $key => $string){
      $content = Str_Replace(SPrintF($this->cache_expression, $key), $string, $content);
    }
    return $content;
  }

  function setSkipElements($elements){
    $elements = Is_Array($elements) ? $elements : Array();
    $this->skip_elements = $elements;
  }

  function linkCompleteWordsOnly($state = True){
    $this->link_complete_words_only = (Boolean) $state;
  }

  function replacePhrasesOnce($state = True){
    $this->replace_phrases_once = (Boolean) $state;
  }

  function setLinkTarget($target){
    $this->link_target = $target;
  }

  function linkPhrase($phrase, $title, $url){
    # Check if there is a valid XPath object available
    If (!$this->XPath) return False;

    # Prepare search term
    $phrase = Trim($phrase);
    $phrase = WPTexturize($phrase); # This is necessary because the content runs through this filter, too
    $phrase = HTML_Entity_Decode($phrase, ENT_QUOTES, 'UTF-8');

    # Prepare search
    $word_boundary = '^|\W|$';
    $search_regex = $this->link_complete_words_only ? SPrintF('/(%1$s)(%%s)(%1$s)/imsuU', $word_boundary) : SPrintF('/(%1$s)(%%s)/imsuU', $word_boundary);
    $search = SPrintF($search_regex, PReg_Quote(HTMLSpecialChars($phrase), '/'));
    $link = SPrintF('$1<a href="%1$s" target="%2$s" title="%3$s" class="encyclopedia">$2</a>$3', $url, $this->link_target, Esc_Attr(HTMLSpecialChars($title)));

    # Go through nodes and replace
    $xpath_query = '//text()[not(ancestor::*[contains(@class,"no-cross-linking")])]';
    ForEach ($this->skip_elements As $skip_element) $xpath_query .= SPrintF('[not(ancestor::%s)]', $skip_element);
    ForEach ($this->XPath->Query($xpath_query) As $original_node){
      $original_text = HTMLSpecialChars(HTML_Entity_Decode($original_node->wholeText, ENT_QUOTES, 'UTF-8'));
      $new_text = @PReg_Replace($search, $link, $original_text, ($this->replace_phrases_once ? 1 : -1)); # This could break if your terms contains very secial character which break the search regex
      If ($new_text != $original_text){
        $new_node = $this->DOM->createDocumentFragment();
        If (@$new_node->appendXML($new_text)){ # If the $new_text is not valid XML this will break
          $original_node->parentNode->replaceChild($new_node, $original_node);
        }
        If ($this->replace_phrases_once) Break; # We only replace the first match of this term with a link
      }
    }
  }

  function getParserDocument(){
    If (!$this->DOM) return False;
    $resultHTML = $this->DOM->saveHTML();

    $head_start = MB_StrPos($resultHTML, '<head>', 0, 'UTF-8') + 6;
    $head_end = MB_StrPos($resultHTML, '</head>', $head_start, 'UTF-8');
    $head = ($head_start > 6 && $head_end) ? MB_SubStr($resultHTML, $head_start, $head_end - $head_start) : '';

    $body_start = MB_StrPos($resultHTML, '<body>', 0, 'UTF-8') + 6;
    $body_end = MB_StrPos($resultHTML, '</body>', $body_start, 'UTF-8');
    $body = ($body_start > 6 && $body_end) ? MB_SubStr($resultHTML, $body_start, $body_end - $body_start) : '';

    $html = $this->unCacheStrings($head . $body);
    return $html;
  }

}