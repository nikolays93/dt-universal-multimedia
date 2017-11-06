<li tabindex="0" aria-label="{{filename}}" data-id="{{attachment_id}}" class="attachment">
    <div class="thumbnail-wrap">
        <div class="{{attachment_class}}">
            <div class="thumbnail">
                <div class="centered"><img src="{{attachment_url}}" alt=""></div>
            </div>
        </div>
        <button type="button" class="check remove" tabindex="-1">
            <span class="media-modal-icon"></span>
        </button>
        <input type="text" class="item-excerpt" name="attachment_excerpt[{{attachment_id}}]" value="{{attachment_excerpt_value}}">
    </div><!-- .thumbnail-wrap -->
    <textarea class="item-content" name="attachment_content[{{attachment_id}}]" cols="75" rows="7" placeholder="The some contents..">{{attachment_content_value}}</textarea>
    <div class="item-link-wrap">
        <input type="text" class="item-link" name="attachment_link[{{attachment_id}}]" value="{{attachment_link_value}}">
        <label class="open-blank">
            {{attachment_blank_label}}
            <input type="checkbox" class="item-blank" value="1" name="attachment_blank[{{attachment_id}}]"{{attachment_blank_checked}}>
        </label>
    </div>
    <input type="hidden" id="attachments" name="attachment_id[]" value="{{attachment_id}}">
</li>
