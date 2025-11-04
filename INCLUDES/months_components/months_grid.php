<div class="container">
    <div class="row g-4">
        <?php foreach ($months as $month_num => $month_name) {
            include '../INCLUDES/month_card.php'; // Reutiliza o card que já existe
        } ?>
    </div>
</div>