<?php

namespace KooijmanInc\Suzie\Model\Connection;

use KooijmanInc\Suzie\Helper\Common;
use mysqli;
use mysqli_stmt;
use Psr\Log\LoggerInterface;

class MySql
{
    /**
     * @var mysqli
     */
    private mysqli $conn;

    /**
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     * @param string $dbname
     * @param string $charset
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly string $host, private readonly string $user, private readonly string $pass, private readonly string $port, private readonly string $dbname, private readonly string $charset, private LoggerInterface $logger)
    {

    }

    /**
     * @return void
     */
    public function connect(): void
    {
        if (isset($this->database)) {
            $this->conn = $this->setConnection($this->database);
        } else {
            $this->conn = $this->setConnection($this->dbname);
        }
    }

    /**
     * @param string $sql
     * @param string|null $col
     * @param array $bind
     * @return array
     */
    protected function getAll(string $sql, string $col = null, array $bind = []): array
    {
        if (false !== $res = $this->query($sql)) {
            if ($col !== null && $col !== "") {
                $res->bind_param($col, ...$bind);
            }

            $res->execute();
            $result = $res->get_result();

            while ($rec = $result->fetch_assoc()) {
                $list[] = $rec;
            }

            if (isset($list)) {
                return $list;
            } else {
                $this->logger->notice("[" . date("Y-m-d H:i:s") . "] get all has found nothing $sql");
                return [];
            }
        } else {
            $this->logger->warning("[" . date("Y-m-d H:i:s") . "] get all query has failed $sql");
            return [];
        }
    }

    protected function getOne(string $sql, string $col = null, array $bind = []): array
    {
        if (false !== $res = $this->query($sql)) {
            if ($col !== null && $col !== "") {
                $res->bind_param($col, ...$bind);
            }

            $res->execute();
            $result = $res->get_result();

            if ($found = $result->fetch_assoc()) {
                return $found;
            } else {
                $this->logger->notice("[" . date("Y-m-d H:i:s") . "] get one has found nothing $sql " . implode(' ', $bind));
                return [];
            }
        } else {
            $this->logger->warning("[" . date("Y-m-d H:i:s") . "] get one query has failed $sql");
            return [];
        }
    }

    protected function insertRow(string $sql, string $col = null, array $bind = []): int
    {
        if (false !== $res = $this->query($sql)) {
            if ($col !== null && $col !== "") {
                $res->bind_param($col, ...$bind);
            }

            if ($res->execute()) {
                return $this->conn->insert_id;
            } else {
                $this->logger->notice("[" . date("Y-m-d H:i:s") . "] insert row has found nothing $sql");
                return 0;
            }
        } else {
            $this->logger->warning("[" . date("Y-m-d H:i:s") . "] insert row query has failed $sql");
            return 0;
        }
    }

    /**
     * @param string $sql
     * @return false|mysqli_stmt
     */
    private function query(string $sql): bool|mysqli_stmt
    {
        if (!$result = $this->conn->prepare($sql)) {
            $this->logger->warning("[".date("Y-m-d H:i:s")."] prepare $sql failed");
        }

        return $result;
    }

    /**
     * @param string $db
     * @return mysqli
     */
    private function setConnection(string $db): mysqli
    {
        return new mysqli(Common::decrypt($this->host), Common::decrypt($this->user), Common::decrypt($this->pass), $db, Common::decrypt($this->port), Common::decrypt($this->charset));
    }
}