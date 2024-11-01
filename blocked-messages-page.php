<div class="wrap">
    <h1> <?php _e('Blocked messages'); ?> </h1>

    <div class="clear"></div>
    
    <hr/>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th> <?php _e('Date'); ?> </th>
                <th> <?php _e('Email'); ?> </th>
                <th> <?php _e('Message'); ?> </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $obj): ?>
                <tr>
                    <td> <?php echo date("d/m/Y H:i", $obj->time); ?> </td>
                    <td> <?php echo $obj->email ? : '-'; ?> </td>
                    <td> <?php echo $obj->message; ?> </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>