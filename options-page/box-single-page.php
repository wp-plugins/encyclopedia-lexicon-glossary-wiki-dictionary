<table class="form-table">
<tr>
  <th><label for="related_terms"><?php Echo $this->t('Display related entries') ?>:</label></th>
  <td>
		<input type="radio" id="related_terms_below" disabled="disabled"> <label for="related_terms_below"><?php Echo $this->t('below the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_above" checked="checked"> <label for="related_terms_above"><?php Echo $this->t('above the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_none" disabled="disabled"> <label for="related_terms_none"><?php Echo $this->t('do not show related terms') ?></label>
    <p class="pro-notice"><?php $this->Pro_Notice() ?></p>
	</td>
</tr>

</table>