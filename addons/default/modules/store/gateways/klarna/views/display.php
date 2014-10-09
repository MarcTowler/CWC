<h2 id="page_title">Klarna</h2>

<div class="store-container">
    <?php if (isset($options['error'])): ?>
        <div class="error"> 
            <?php echo ($options['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php echo ($options['snippet']); ?>
</div>