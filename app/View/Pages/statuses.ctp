<div class="mm-page">
    <ul class="mm-help">
        <!-- nav for: statuses -->
        <li class="mm-help-prev">
            <?php echo $this->Html->link('<span>' . __('&laquo;previous') . '</span><br/>' . __('Viewing'), array('action' => 'viewing'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-contents">
            <?php echo $this->Html->link(__('Help') . '<br/>' . __('Contents'), array('action' => 'help'), array('escape' => false)); ?>
        </li>
        <li class="mm-help-next">
            <?php echo $this->Html->link('<span>' . __('next&raquo;') . '</span><br/>' . __('Tags'), array('action' => 'tags'), array('escape' => false)); ?>
        </li>
    </ul>
    <h2>
        Message statuses
    </h2>
    <p>
        In general, FMS shows the messages that are "available" for assigning an FMS ID, together with any replies (this via the <code>available</code> API call).
    </p>
    <p>
        The following table shows all possible states that a message may be in, and a summary of what they mean.
    </p>
    <table style="width:auto;" class="mm-messages">
        <tr>
            <td class="status-available">
                available
            </td>
            <td>
                <strong>message received, not yet assigned an FMS ID</strong>
                <br>
                The message has been received (from a message source) and has not yet been assigned to a problem report &mdash; it is <em>available</em>
                for use in FixMyStreet. These are generally the only messages that are provided over the JSON API and the only messages that an FMS user can lock. A lock is required before an FMS ID can be assigned.
            </td>
        </tr>
        <tr>
            <td class="status-assigned">
                assigned
            </td>
            <td>
                <strong>message has an assigned FMS id</strong>
                <br>
                The message has been used to generate an problem report on FixMyStreet. Problems that have been assigned to an FMS problem report will also have an FMS ID.
            </td>
        </tr>
        <tr>
            <td class="status-hidden">
                hidden
            </td>
            <td>
                <strong>message has been hidden</strong>
                <br>
                Hidden messages do not appear in the list of available messages,
                which means they don't appear within FixMyStreet.
            </td>
        </tr>
        <tr>
            <td class="status-pending">
                pending
            </td>
            <td>
                <strong>message (reply) is waiting to be sent</strong>
                <br>
                The reply that has just been sent from the message manager, but has not yet actually been sent out of the SMS gateway. Outgoing messages
                are sent in batch jobs, so may be in a pending state for a few 
                minutes.
            </td>
        </tr>
        <tr>
            <td class="status-sent">
                sent
            </td>
            <td>
                <strong>message has been sent</strong>
                <br>
                This indicates the message has been sent on systems where 
                there is no mechanism for determining the message status after
                it has been despatched to the message source. See also
                <em>sent_pending</em>, <em>sent_ok</em>, <em>sent_unknown</em>,
                and <em>sent_fail</em>.
            </td>
        </tr>
        <tr>
            <td class="status-sent_pending">
                sent_pending
            </td>
            <td>
                <strong>message has been delivered to the SMS gateway, but has
                    not yet been despatched</strong>
                <br>
                This is usually a transition state. The Message Manager checks the
                status of messages on the gateway every few minutes.
            </td>
        </tr>
        <tr>
            <td class="status-sent_ok">
                sent_ok
            </td>
            <td>
                <strong>message has been sent (gateway confirmed)</strong>
                <br>
                The message has been despatched to the gateway, and furthermore
                the gateway has confirmed that it has successfully been sent.
                This is the conclusive "success" state for an outgoing message.
            </td>
        </tr>
        <tr>
            <td class="status-sent_unknown">
                sent_unknown
            </td>
            <td>
                <strong>message is in an unknown state at the gateway</strong>
                <br>
                The message has been despatched to the gateway, but the gateway
                was unable to confirm what happened to it afterwards.

            </td>
        </tr>
        <tr>
            <td class="status-sent_fail">
                sent_fail
            </td>
            <td>
                <strong>message failed to leave the gateway</strong>
                <br>
                The message was despatched to the gateway, but it subsequently 
                failed to be sent. This may occur if the destination of the
                reply is not a valid MSIDN or phone number, for example.
            </td>
        </tr>
        <tr>
            <td class="status-error">
                error
            </td>
            <td>
                <strong>an error occurred</strong>
                <br>
                Something unexpected happened to the message. This generally will
                not be a problem that occurs at the gateway, since those are reported
                as <em>sent_*</em> errors.
            </td>
        </tr>
        <tr>
            <td class="status-unknown">
                unknown
            </td>
            <td>
                <strong>message is in an unknown state</strong>
                <br>
                This is an error state.
            </td>
        </tr>
    </table>
</div>
