<% loop $Options %>
  <div class="form-check $Class $Up.extraClass" role="$Role">
    <input id="$ID" class="form-check-input" name="$Name" type="checkbox" value="$Value.ATT"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> />
    <label class="form-check-label" for="$ID">$Title</label>
  </div>
<% end_loop %>