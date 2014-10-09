<h2 id="page_title">Bank Wire</h2>

<div class="transfer-form">
    <ul>
        <li>
            <label>Account Number</label>
            <div class="input">
                <input type="text" value="<?php echo $options['account']; ?>" readonly="true" contenteditable="false" />
            </div>
        </li>
        <li>
            <label>Bank BIC</label>
            <div class="input">
                <input type="text" value="<?php echo $options['bic']; ?>" readonly="true" contenteditable="false" />
            </div>
        </li>
        <li>
            <label>Transfer title</label>
            <div class="input">
                <input type="text" value="<?php echo $order->id . '/' . date('Y/m/d', $order->order_date); ?>" readonly="true" contenteditable="false" />
            </div>
        </li>
        <li>
            <label>Transfer receiver</label>
            <div class="input">
                <input type="text" value="<?php echo $options['receiver']; ?>" readonly="true" contenteditable="false" />
            </div>
        </li>
        <li>
            <label>Transfer receiver address</label>
            <div class="input">
                <textarea readonly="true" contenteditable="false" cols="40" rows="4" ><?php echo $options['address']; ?></textarea>
            </div>
        </li>

        <li>
            <label>Amount to transfer</label>
            <div class="input">
                <input type="text" value="<?php echo ($order->shipping + $order->total); ?>" readonly="true" contenteditable="false" /><?php echo Settings::get('currency_symbol'); ?>
            </div>
        </li>

        <li>
            <label>&nbsp;</label>
            <div class="input">
                <button id="print">Print</button>
            </div>
        </li>
    </ul>
</div>

<script>
    (function() {
        $("input[type=text], textarea").focus(function(){
            this.select();
        });
        $('#print').click(function () {
            w=window.open();
            w.document.write($('.transfer-form').html());
            w.print();
            w.close();
        });
        
    })(jQuery)
</script>