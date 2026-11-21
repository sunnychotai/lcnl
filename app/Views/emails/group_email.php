<?php
/**
 * A group email written in the admin (App\Services\GroupEmailService).
 * $content is the admin's HTML with placeholders already filled and escaped.
 */
?>
<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>
<?= $content ?>
<?= $this->endSection() ?>
