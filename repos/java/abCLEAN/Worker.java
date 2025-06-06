public class Worker
{
   private String wkrName, wkrId, wkrContact ;
   
   public Worker(String wkrName, String wkrId, String wkrContact)
   {
       this.wkrName = wkrName ;
       this.wkrId = wkrId ;
       this.wkrContact = wkrContact ;
   }
   
   public void setWorker (String wn, String wi, String wc)
   {
       wkrName = wn;
       wkrId = wi ;
       wkrContact = wc ;
   }
   
   public String getWorkerName()
   {
       return wkrName ;
   }
   public String getWorkerId()
   {
       return wkrId ;
   }
   public String getWorkerContact()
   {
       return wkrContact ;
   }
}