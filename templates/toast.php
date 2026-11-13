<?php if (!empty($mensagem)): ?>
    <div id="mensagem" class="toast hidden">
        <?php echo htmlspecialchars($mensagem) ?>
    </div>
<?php endif; ?>