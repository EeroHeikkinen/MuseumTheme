{* This is a text-only email template; do not include HTML! *}
{translate text="Message From Sender"}: {if $name}{$name}{else}{translate text="Anonymous"}{/if} {if $email}<{$email}>{/if}

{translate text="feedback_category"}: {$category}
{if $url}{translate text="feedback_url"}: {$feedback_url}{/if}

------------------------------------------------------------

{$message}

------------------------------------------------------------
