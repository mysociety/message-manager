<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: overview -->
        <li class="mm-help-prev">&nbsp;
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Messages'), array('action' => 'messages'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Message Manager overview
    </h2>
    <p>
        
    <p>
        Message Manager connects one or message sources with a service such as FixMyStreet.
        It acts as an in-between layer, allowing incoming messages to be offered to staff using
        the web-based FixMyStreet interface. They can then use these messages to create new problem 
        reports. This effectively means users can submit problems to FixMyStreet via non-web methods 
        &mdash; such as SMS messages.
    </p>
    <p>
        In addition to receiving messages and assigning them to problem reports, Message Manager also
        allows staff to send replies, hide, and manipulate the messages.
    </p>
    <img src="/img/system-overview.png" alt="diagram showing MM system"/>
    <p>
        Messages are typically received or sent to the Message Manager as regular (cron) batched jobs.
        The Message Manager provides a JSON API that lets FixMyStreet use AJAX calls to embed the 
        messages within the FixMyStreet web interface.
    </p>
    <p>
        The server software is written in PHP (Cake framework), and the client-side code, embedded in 
        FixMyStreet, is in Javascript (using JQuery).
    </p>
    
</div>
