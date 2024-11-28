<?php
require_once "connection.php";

class ModelsImportar {

	static public function createTable($pdo) {
		$sql = "
		CREATE TABLE IF NOT EXISTS slow_logs (
			insert_id BIGINT AUTO_INCREMENT PRIMARY KEY, 
			last_insert_id BIGINT NOT NULL,
			time TIMESTAMP(6) NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
			user_host MEDIUMTEXT NOT NULL,
			query_time TIME(6) NOT NULL,
			lock_time TIME(6) NOT NULL,
			rows_sent INT NOT NULL,
			rows_affected INT NOT NULL,
			rows_examined INT NOT NULL,
			name_schema VARCHAR(512) NOT NULL,
			sql_text MEDIUMBLOB NOT NULL,
			thread_id BIGINT UNSIGNED NOT NULL,
			errno INT DEFAULT 0, 
			killed TINYINT DEFAULT 0,
			bytes_received INT DEFAULT 0,
			bytes_sent INT DEFAULT 0,
			read_first INT DEFAULT 0,
			read_last INT DEFAULT 0,
			read_key INT DEFAULT 0,
			read_next INT DEFAULT 0,
			read_prev INT DEFAULT 0,
			read_rnd INT DEFAULT 0,
			read_rnd_next INT DEFAULT 0,
			sort_merge_passes INT DEFAULT 0,
			sort_range_count INT DEFAULT 0,
			sort_rows INT DEFAULT 0,
			sort_scan_count INT DEFAULT 0,
			created_tmp_disk_tables INT DEFAULT 0,
			created_tmp_tables INT DEFAULT 0,
			start TIMESTAMP(6),
			end TIMESTAMP(6),
			main_table VARCHAR(100)
		)";
		$pdo->exec($sql);
	
		$pdo->exec("CREATE INDEX idx_slow_logs_time ON slow_logs(time)");
		$pdo->exec("CREATE INDEX idx_slow_logs_user_host ON slow_logs(user_host(255))");
		$pdo->exec("CREATE INDEX idx_slow_logs_query_time ON slow_logs(query_time)");
		$pdo->exec("CREATE INDEX idx_slow_logs_lock_time ON slow_logs(lock_time)");
		$pdo->exec("CREATE INDEX idx_slow_logs_rows_sent ON slow_logs(rows_sent)");
		$pdo->exec("CREATE INDEX idx_slow_logs_rows_affected ON slow_logs(rows_affected)");
		$pdo->exec("CREATE INDEX idx_slow_logs_rows_examined ON slow_logs(rows_examined)");
		$pdo->exec("CREATE INDEX idx_slow_logs_name_schema ON slow_logs(name_schema(255))");
		$pdo->exec("CREATE INDEX idx_slow_logs_sql_text ON slow_logs(sql_text(255))");
		$pdo->exec("CREATE INDEX idx_slow_logs_start ON slow_logs(start)");
		$pdo->exec("CREATE INDEX idx_slow_logs_end ON slow_logs(end)");
		$pdo->exec("CREATE INDEX idx_slow_logs_main_table ON slow_logs(main_table)");
	}

	static public function gravaLogs($pdo, $dadosMinerados) {

		$pdo->exec("DROP DATABASE IF EXISTS slow_logs_db");
		$pdo->exec("CREATE DATABASE IF NOT EXISTS slow_logs_db");
		$pdo->exec("USE slow_logs_db");
	
		self::createTable($pdo);

		$stmt = $pdo->prepare("
			INSERT INTO slow_logs (
				time, user_host, query_time, lock_time, rows_affected, rows_sent, rows_examined, name_schema, last_insert_id, sql_text, thread_id, errno, killed, bytes_received, bytes_sent, read_first, read_last, read_key, read_next, read_prev, read_rnd, read_rnd_next, sort_merge_passes, sort_range_count, sort_rows, sort_scan_count, created_tmp_disk_tables, created_tmp_tables, start, end, main_table
			) VALUES (
				:time, :user_host, :query_time, :lock_time, :rows_affected, :rows_sent, :rows_examined, :name_schema, LAST_INSERT_ID(), :sql_text, :thread_id, :errno, :killed, :bytes_received, :bytes_sent, :read_first, :read_last, :read_key, :read_next, :read_prev, :read_rnd, :read_rnd_next, :sort_merge_passes, :sort_range_count, :sort_rows, :sort_scan_count, :created_tmp_disk_tables, :created_tmp_tables, :start, :end, :main_table
			)
		");
		
		foreach ($dadosMinerados as $reg) {
			// ignorar time não informado
			if (!isset($reg['time'])) {
				continue;
			}

			$stmt->execute([
				':time' => isset($reg['time']) 
					? (
						($date = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['time']) ?: DateTime::createFromFormat('Y-m-d H:i:s', $reg['time']) ?: DateTime::createFromFormat('ymd H:i:s', $reg['time'])) 
						? $date->format('Y-m-d H:i:s.u') 
						: '1970-01-01 00:00:01.000000'
					) 
					: '1970-01-01 00:00:01.000000',
				':user_host' => isset($reg['user']) && isset($reg['host']) ? $reg['user'] . '@' . $reg['host'] : null,
				':query_time' => isset($reg['query_time']) ? $reg['query_time'] : null,
				':lock_time' => isset($reg['lock_time']) ? $reg['lock_time'] : null,
				':rows_sent' => $reg['rows_sent'] ?? null,
				':rows_affected' => $reg['rows_affected'] ?? 0,
				':rows_examined' => $reg['rows_examined'] ?? null,
				':name_schema' => $reg['name_schema'] ?? null,
				':sql_text' => $reg['query'] ?? null,
				':thread_id' => $reg['thread_id'] ?? null,
				':errno' => $reg['errno'] ?? 0,
				':killed' => $reg['killed'] ?? 0,
				':bytes_received' => $reg['bytes_received'] ?? 0,
				':bytes_sent' => $reg['bytes_sent'] ?? 0,
				':read_first' => $reg['read_first'] ?? 0,
				':read_last' => $reg['read_last'] ?? 0,
				':read_key' => $reg['read_key'] ?? 0,
				':read_next' => $reg['read_next'] ?? 0,
				':read_prev' => $reg['read_prev'] ?? 0,
				':read_rnd' => $reg['read_rnd'] ?? 0,
				':read_rnd_next' => $reg['read_rnd_next'] ?? 0,
				':sort_merge_passes' => $reg['sort_merge_passes'] ?? 0,
				':sort_range_count' => $reg['sort_range_count'] ?? 0,
				':sort_rows' => $reg['sort_rows'] ?? 0,
				':sort_scan_count' => $reg['sort_scan_count'] ?? 0,
				':created_tmp_disk_tables' => $reg['created_tmp_disk_tables'] ?? 0,
				':created_tmp_tables' => $reg['created_tmp_tables'] ?? 0,
				':start' => isset($reg['start']) ? (
					($startDate = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['start'])) 
					? $startDate->format('Y-m-d H:i:s.u') 
					: null
				) : null,
				':end' => isset($reg['end']) ? (
					($endDate = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $reg['end'])) 
					? $endDate->format('Y-m-d H:i:s.u') 
					: null
				) : null,
				':main_table' => $reg['main_table'] ?? null
			]);
		}
	}
}