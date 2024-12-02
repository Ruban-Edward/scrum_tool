<?php

/**
 * NotesService.php
 *
 * @author Jeril
 * @category Service
 *
 *
 * This class provides a service layer for managing notes in the application. It acts as an intermediary
 * between the controller and the model, encapsulating the business logic related to notes. The service class
 * is responsible for validating input data, interacting with the `NotesModel` to perform CRUD operations, and
 * providing a structured way to handle notes-related requests.
 *
 * Class NotesService
 *
 * @package App\Services
 */

namespace App\Services;
use CodeIgniter\RESTful\ResourceController;

class NotesService extends ResourceController
{
    protected $notes;
    private $NotesModel;
    private $SprintModel;
    private $currentDateTime;
    private $historyModel;
    public function __construct()
    {
        $this->NotesModel = model(\App\Models\NotesModel::class);
        $this->SprintModel = model(\App\Models\SprintModel::class);
        $this->historyModel = model(\App\Models\HistoryModel::class);

        $this->currentDateTime = Date('Y-m-d H:i:s');
    }

    public function insertNotes($data)
    {
        $validationErrors = $this->hasInvalidInput($this->NotesModel, $data);
        if ($validationErrors !== true) {
            return false;
        }
        return $this->NotesModel->insertNotes($data);
    }
    public function getNotes($data)
    {
        return $this->NotesModel->getNotes($data);
    }
}
