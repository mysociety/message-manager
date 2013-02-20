<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: strings -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Changing password'), array('action' => 'password'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Activity (logs)'), array('action' => 'activity'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Boilerplate strings
    </h2>
    <p>
        Boilerplate strings are prepared texts that users can use when hiding or replying to a message within FixMyStreet. There are two
        kinds of boilerplate strings: <em>hide-reasons</em> and <em>replies</em>. Note that although you can 
        <?php echo $this->Html->link(__('hide messages'), array('action' => 'hiding')); ?> or
        <?php echo $this->Html->link(__('reply to messages'), array('action' => 'replying')); ?>
        from within the Message Manager, currently the boilerplate strings are only available from within FixMyStreet.
    </p>
    <p>
        To view the strings, click on <strong>Strings</strong> in the navigation menu.
    </p>
    <h3>
        Adding and editing strings
    </h3>
    <p>
        You need to be logged in with an admin or manager account in order to add or edit a string. View the strings and either click on
        <strong>Add a new string</strong> or <strong>Edit</strong>.
    </p> 
    </p>
        Make sure you choose one of the existing string types. If you don't, then your string won't be included in either of the drop-down menus,
        so will never be used.
    </p>
    <p> 
        Specify a language code. The language codes are used to separate the strings by language in the drop-down 
        menu that appears in FixMyStreet.
    </p>
    <p>
        If the string is of type <code>reply</code>, you can add three dots (<code>...</code>) to indicate that it is a prefix of a suffix.
        This means it will be added at the start or end of the message (if any), respectively. The ad
    </p>
    <h3>
        Deleting a string
    </h3>
    <p>
        You need to be logged in with an admin or manager account in order to add or edit a string. Find the string and click on
        <strong>Delete</strong>.
    </p>
    <h3>
        Note about caching boilerplate strings
    </h3>
    <p>
        FixMyStreet generally caches the boilerplate strings at the start of the <em>browser</em> session (since it's not expected that
        they will change frequently or urgently). If you make any changes to the strings in Message Manager, it's likely that they will
        not appear in FixMyStreet for any specific user who was already logged in when you did so. They will probably need to close and reopen 
        their browser before the changes are picked up.
    </p>
        
</div>
