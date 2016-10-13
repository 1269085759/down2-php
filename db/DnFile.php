<?php
/*
	说明：
		1.在调用此函数前不能有任何输出操作。比如 echo print
		
	更新记录：
		2014-09-01 创建
*/
class DnFile 
{
	var $db;//全局数据库连接,共用数据库连接
	
	function __construct() 
	{
		$this->db = new DbHelper();
	}

    function Add(&$inf)
    {
        $idSvr = 0;		
		$sql  = "insert into down_files(";
		$sql .=" f_uid";
		$sql .=",f_nameLoc";
		$sql .=",f_pathLoc";
		$sql .=",f_fileUrl";
		$sql .=",f_lenSvr";
		$sql .=",f_sizeSvr";
		$sql .=") values(";
		$sql .=" :f_uid";//uid
		$sql .=",:f_nameLoc";//name
		$sql .=",:f_pathLoc";//pathLoc
		$sql .=",:f_fileUrl";//pathSvr
		$sql .=",:f_lenSvr";//lenSvr
		$sql .=",:f_sizeSvr";//sizeSvr
		$sql .=")";
		
		$cmd = $this->db->prepare_utf8( $sql );

		$cmd->bindValue(":f_uid",$inf->uid,PDO::PARAM_INT);
		$cmd->bindParam(":f_nameLoc",$inf->nameLoc);
		$cmd->bindParam(":f_pathLoc",$inf->pathLoc);
		$cmd->bindParam(":f_fileUrl",$inf->fileUrl);
		$cmd->bindValue(":f_lenSvr",$inf->lenSvr,PDO::PARAM_INT);
		$cmd->bindParam(":f_sizeSvr",$inf->sizeSvr);					
		$idSvr = $this->db->ExecuteGenKey($cmd,"f_id");

		return $idSvr;
    }

    /**
     * 将文件设为已完成
     * @param fid
     */
    function Complete($fid)
    {
		$db = new DbHelper();
		$cmd =& $db->GetCommand("update down_files set f_complete=1 where f_id=?;");
		$cmd->bindParam(1,$fid);
		$db->ExecuteNonQuery($cmd);
    }

    /// <summary>
    /// 删除文件
    /// </summary>
    /// <param name="fid"></param>
    function Delete($fid,$uid)
    {
        $sql = "delete from down_files where f_id=:f_id and f_uid=:f_uid";
		
		$cmd = $this->db->prepare_utf8($sql);

		$cmd->bindParam(":f_id", $fid);
		$cmd->bindParam(":f_uid", $uid);
		$this->db->Execute($cmd);
    }

    /**
     * 更新文件进度信息
     * @param fid
     * @param uid
     * @param mac
     * @param lenLoc
     */
    function updateProcess($fid,$uid,$lenLoc,$perLoc)
    {
       $sql = "update down_files set f_lenLoc=:f_lenLoc,f_perLoc=:f_perLoc where f_id=:f_id and f_uid=:f_uid";
		$cmd = $this->db->prepare_utf8($sql);		

		$cmd->bindParam(":f_lenLoc", $lenLoc);
		$cmd->bindParam(":f_perLoc", $perLoc);
		$cmd->bindParam(":f_id", $fid);
		$cmd->bindParam(":f_uid", $uid);
		
		$this->db->Execute($cmd);
    }
	
	function clear()
	{
		$this->db->ExecuteNonQueryTxt("truncate table down_files");
	}

    /// <summary>
    /// 获取所有未完成的文件列表
    /// </summary>
    /// <returns></returns>
    function GetAll($uid)
    {
        $sql = "select * from down_files where f_uid=:f_uid and f_complete=0";

        $files = array();
		$db = new DbHelper();
		$cmd =& $db->GetCommand($sql);
		$cmd->bindParam(":f_uid",$uid);
		$ret = $db->ExecuteDataSet($cmd);
		foreach($ret as $row)
		{
			$f = new DnFileInf();
			$f->idSvr		= (int)$row["f_id"];
			$f->nameLoc		= $row["f_nameLoc"];
			$f->pathLoc		= $row["f_pathLoc"];
			$f->fileUrl		= $row["f_fileUrl"];
			$f->perLoc		= $row["f_perLoc"];
			$f->lenLoc		= (int)$row["f_lenLoc"];
			$f->lenSvr		= (int)$row["f_lenSvr"];
			$f->sizeSvr		= $row["f_sizeSvr"];
			
			array_push($files,$f);
		}
	    return json_encode($files);
	}
}