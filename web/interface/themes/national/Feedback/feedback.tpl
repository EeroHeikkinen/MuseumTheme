<!-- START of: Feedback/feedback.tpl -->

<div class="contentHeader"><div class="content"><h1>{translate text='feedback_heading'}</h1></div></div>
<div class="content">

{if not $submitted}
{if $errorMsg}<p class="error">{$errorMsg}</p>{/if}
<form id="feedbackForm" action="" method="post">
    <label for="category">{translate text="Subject"}:</label>
    <select name="category" id="category">
        <option {if $category=='feedback_category_general'}selected="selected {/if}value="feedback_category_general">{translate text='feedback_category_general'}</option>
        <option {if $category=='feedback_category_metadata'}selected="selected {/if}value="feedback_category_metadata">{translate text='feedback_category_metadata'}</option>
        <option {if $category=='feedback_category_services'}selected="selected {/if}value="feedback_category_services">{translate text='feedback_category_services'}</option>
        <option {if $category=='feedback_category_usability'}selected="selected {/if}value="feedback_category_usability">{translate text='feedback_category_usability'}</option>
        <option {if $category=='feedback_category_technical'}selected="selected {/if}value="feedback_category_technical">{translate text='feedback_category_technical'}</option>
    </select>
    <label for="message">{translate text='Message'}:</label>
    <textarea cols="48" rows="10" id="message" name="message" class="{jquery_validation required='This field is required'}">{$message}</textarea>
    <label id="urlLabel" for="url">{translate text='feedback_url'}:</label>
    <input type="text" id="url" name="feedback_url" value="{$feedback_url}" />
    <p>{translate text='feedback_info'}</p>
    <label for="name">{translate text='feedback_name'}:</label>
    <input type="text" id="name" name="name" value="{$name}" />
    <label for="email">{translate text='Email'}:</label>
    <input type="text" id="email" name="email" class="{jquery_validation email='Email address is invalid'}" value="{$from}" />
    <p>{translate text='feedback_info_captcha'}</p>
    <label for="question">{translate text='feedback_captcha_question'}:</label>
    <input type="text" id="question" name="question" class="{jquery_validation required='This field is required'}" value="" />
    <br />
    <input type="submit" id="submit" name="submit" class="button buttonTurquoise" value="{translate text='Send'}" />
</form>
<script type="text/javascript">
  {literal}
  $(document).ready(function() {
    $('#feedbackForm').validate();
    {/literal}
    {if $captchaError}{literal}
    $('#question').addClass('invalid');
    $('#question').bind('focus', function() {
        $(this).removeClass('invalid');
    });
    {/literal}{/if}{literal}
  });
  {/literal}
</script>
{else}
<p>{translate text="feedback_thankyou"}</p>
{/if}
</div>

<!-- END of: Feedback/feedback.tpl -->
