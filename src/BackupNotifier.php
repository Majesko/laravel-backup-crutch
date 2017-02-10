<?php

namespace BackupNotifier;

use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Notifications\Notifier;
use Spatie\Backup\Notifications\BaseSender;
use Exception;

class BackupNotifier extends Notifier
{
	// Do not spam us!
	public function backupWasSuccessful() {}

	public function backupHasFailed(Exception $exception, BackupDestination $backupDestination = null) {}

	public function correctBackupWasSuccessful(BackupWasSuccessful $event)
	{
		$this->sendNotification(
			'whenBackupWasSuccessful',
			$this->subject,
			'Successfully took a new backup to '.$event->backupDestination->getFilesystemType().' for app '.env('APP_NAME'),
			BaseSender::TYPE_SUCCESS
		);
	}

	public function correctBackupHasFailed(BackupHasFailed $event)
	{
		$this->sendNotification(
			'whenBackupHasFailed',
			$this->subject.' : error',
			'Failed to backup for app '.env('APP_NAME'),
			BaseSender::TYPE_ERROR
		);
	}
}
