public class Personal extends CustomerServices
{
    private double discount;
    
    public Personal()
    {
        super();
        discount = 0.0;
    }
    
    public void setPersonal(String cn, String pn, String ad, String st, double sa, double ta, Worker w, double d)
    {
        setCustomerServices(cn, pn, ad, st, sa, ta, w);
        discount = d;
    }
    
    public double getDiscount()
    {
        return discount;
    }
    
    public double calcServiceCharge() // overridding (same name as processor in customer services)
    {
        return super.calcServiceCharge() * discount;
    }
    
    /*public String toString() //if theres no toString in superclass
    {
        return super.getCustName() + super.getPhoneNo() + super.getAddress() + super.getServiceType() + super.getServiceArea() + super.getToiletArea() + super.getWorker() + discount ;
    }*/
    
    public String toString()
    {
        return super.toString() + discount; //if the superclass have toString
    }
}