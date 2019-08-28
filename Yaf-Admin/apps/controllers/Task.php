<?php
/**
 * 打卡任务管理。
 * @author fingerQin
 * @date 2019-08-28
 */

use finger\Paginator;
use Services\System\Record;
use Services\System\Sponsor;

class TaskController extends \Common\controllers\Admin
{
    /**
     * 主办方列表。
     */
    public function sponsorAction()
    {
        $name      = $this->getString('name', '');
        $page      = $this->getInt('page', 1);
        $result    = Sponsor::lists($name, $page, 20);
        $paginator = new Paginator($result['total'], 20);
        $pageHtml  = $paginator->backendPageShow();
        $this->assign('name', $name);
        $this->assign('pageHtml', $pageHtml);
    }

    /**
     * 添加主办方。
     */
    public function addSponsorAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $name         = $this->getString('name');
            $address      = $this->getString('address');
            $districtCode = $this->getString('district_code');
            $albums       = $this->getArray('albums', []);
            $longitude    = $this->getFloat('longitude');
            $latitude     = $this->getFloat('latitude');
            Sponsor::add($name, $address, $districtCode, $albums, $longitude, $latitude, $this->adminId);
            $this->json(true, '添加成功');
        }
    }

    /**
     * 编辑主办方。
     */
    public function editSponsorAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $sponsorId    = $this->getInt('sponsorid');
            $name         = $this->getString('name');
            $address      = $this->getString('address');
            $districtCode = $this->getString('district_code');
            $albums       = $this->getArray('albums', []);
            $longitude    = $this->getFloat('longitude');
            $latitude     = $this->getFloat('latitude');
            Sponsor::edit($sponsorId, $name, $address, $districtCode, $albums, $longitude, $latitude, $this->adminId);
            $this->json(true, '编辑成功');
        } else {
            $sponsorId = $this->getInt('sponsorid');
            $detail    = Sponsor::detail($sponsorId);
            $this->assign('detail', $detail);
        }
    }

    /**
     * 删除主办方。
     */
    public function deleteSponsorAction()
    {
        $sponsorId = $this->getInt('sponsorid');
        Sponsor::delete($sponsorId, $this->adminId);
        $this->json(true, '删除成功');
    }

    /**
     * 打卡任务列表。
     */
    public function taskListAction()
    {
        $sponsorId = $this->getInt('sponsorid');
        $taskName  = $this->getString('task_name', '');
        $page      = $this->getInt('page', 1);
        $result    = \Services\Task\Task::lists($sponsorId, $taskName, $page, 20);
        $paginator = new Paginator($result['total'], 20);
        $pageHtml  = $paginator->backendPageShow();
        $this->assign('pageHtml', $pageHtml);
        $this->assign('task_name', $taskName);
        $this->assign('sponsorid', $sponsorId);
    }

    /**
     * 添加打卡任务。
     */
    public function addTaskAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                'sponsorId'      => $this->getInt('sponsorid'),
                'taskName'       => $this->getString('task_name'),
                'address'        => $this->getString('address'),
                'gold'           => $this->getInt('gold'),
                'moveStep'       => $this->getInt('move_step'),
                'timesLimit'     => $this->getInt('times_limit'),
                'longitude'      => $this->getFloat('longitude'),
                'latitude'       => $this->getFloat('latitude'),
                'albums'         => $this->getArray('albums', []),
                'display'        => $this->getInt('display'),
                'start_time'     => $this->getString('start_time'),
                'end_time'       => $this->getString('end_time'),
                'everyday_times' => $this->getInt('everyday_times'),
                'total_times'    => $this->getInt('total_times')
            ];
            \Services\Task\Task::add($data, $this->adminId);
        }
    }

    /**
     * 编辑打卡任务。
     */
    public function editTaskAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $data = [
                'taskId'         => $this->getInt('taskid'),
                'sponsorId'      => $this->getInt('sponsorid'),
                'taskName'       => $this->getString('task_name'),
                'address'        => $this->getString('address'),
                'gold'           => $this->getInt('gold'),
                'moveStep'       => $this->getInt('move_step'),
                'timesLimit'     => $this->getInt('times_limit'),
                'longitude'      => $this->getFloat('longitude'),
                'latitude'       => $this->getFloat('latitude'),
                'albums'         => $this->getArray('albums', []),
                'display'        => $this->getInt('display'),
                'start_time'     => $this->getString('start_time'),
                'end_time'       => $this->getString('end_time'),
                'everyday_times' => $this->getInt('everyday_times'),
                'total_times'    => $this->getInt('total_times')
            ];
            \Services\Task\Task::edit($data, $this->adminId);
            $this->json(true, '保存成功');
        } else {
            $taskId = $this->getInt('taskid');
            $detail = \Services\Task\Task::detail($taskId);
            $this->assign('detail', $detail);
        }
    }

    /**
     * 删除打卡任务。
     *
     * @return void
     */
    public function deleteTaskAction()
    {
        $taskId = $this->getInt('taskid');
        \Services\Task\Task::delete($taskId, $this->adminId);
        $this->json(true, '删除成功');
    }

    /**
     * 打卡记录。
     */
    public function recordsAction()
    {
        $sponsorId = $this->getInt('sponsorid', -1);
        $userid    = $this->getInt('userid', -1);
        $taskId    = $this->getInt('taskId', -1);
        $startTime = $this->getString('start_time', '');
        $endTime   = $this->getString('end_time', '');
        $page      = $this->getInt('page', 1);
        $result    = Record::lists($userid, $taskId, $sponsorId, $startTime, $endTime, $page, 20);
        $paginator = new Paginator($result['total'], 20);
        $pageHtml  = $paginator->backendPageShow();
        $this->assign('pageHtml', $pageHtml);
        $this->assign('userid', $userid);
        $this->assign('sponsorid', $sponsorId);
        $this->assign('taskid', $taskId);
        $this->assign('startTime', $startTime);
        $this->assign('endTime', $endTime);
    }
}