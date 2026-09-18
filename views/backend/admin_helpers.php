<?php

function admin_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_api_url($resource, $action)
{
    return ROOT_URL . '/api/' . $resource . '/' . $action . '.php';
}

function admin_redirect($path)
{
    header('Location: ' . ROOT_URL . $path);
    exit;
}

function admin_required_id($name)
{
    if (!isset($_GET[$name]) || filter_var($_GET[$name], FILTER_VALIDATE_INT) === false || (int) $_GET[$name] < 1) {
        admin_redirect('/views/backend/dashboard.php');
    }

    return (int) $_GET[$name];
}

function admin_query_id($name)
{
    if (!isset($_GET[$name]) || filter_var($_GET[$name], FILTER_VALIDATE_INT) === false || (int) $_GET[$name] < 1) {
        admin_redirect('/views/backend/dashboard.php');
    }

    return (int) $_GET[$name];
}

function admin_user_options()
{
    return sql_select('`USER`', 'idUser, nomEUser, prenomUser, emailUser', null, null, 'idUser ASC');
}

function admin_user_labels($ids)
{
    $labels = array();
    foreach ($ids as $id) {
        $labels[(int) $id] = '#' . (int) $id;
    }

    if (!$ids) {
        return $labels;
    }

    $uniqueIds = array_values(array_unique(array_map('intval', $ids)));
    $where = 'idUser IN (' . implode(',', $uniqueIds) . ')';
    $users = sql_select('`USER`', 'idUser, nomEUser, prenomUser, emailUser', $where);
    foreach ($users as $user) {
        $labels[(int) $user['idUser']] = admin_user_label($user);
    }

    return $labels;
}

function admin_genre_options()
{
    return sql_select('GENRE', 'idGenr, libGenr', null, null, 'libGenr ASC');
}

function admin_genre_labels($ids)
{
    $labels = array();
    foreach ($ids as $id) {
        $labels[(int) $id] = '#' . (int) $id;
    }

    if (!$ids) {
        return $labels;
    }

    $uniqueIds = array_values(array_unique(array_map('intval', $ids)));
    $genres = sql_select('GENRE', 'idGenr, libGenr', 'idGenr IN (' . implode(',', $uniqueIds) . ')');
    foreach ($genres as $genre) {
        $labels[(int) $genre['idGenr']] = $genre['libGenr'];
    }

    return $labels;
}

function admin_user_label($user)
{
    $name = trim($user['prenomUser'] . ' ' . $user['nomEUser']);

    return $name !== '' ? $name . ' (#' . $user['idUser'] . ')' : $user['emailUser'] . ' (#' . $user['idUser'] . ')';
}
