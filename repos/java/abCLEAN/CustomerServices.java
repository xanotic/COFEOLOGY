public class CustomerServices
{
    private String custName, phoneNo, address, serviceType ; //protected can be accessed in subclass
    private double serviceArea, toiletArea ;
    private Worker wrk ;
    
    public CustomerServices()
    {
        custName = "John" ;
        phoneNo = "019" ;
        address = "ABC" ;
        serviceType = "xxx" ;
        serviceArea = 0.0 ;
        toiletArea = 0.0 ;
        wrk = new Worker("Hana", "2023860194", "0192412842") ;
    } 
    
    //ans b ii)
    public void setCustomerServices(String cn, String pn, String ad, String st, double sa, double ta, Worker w)
    {
        custName = cn ; //cn,pn etc is the parameter recieve
        phoneNo = pn ;
        address = ad ;
        serviceType = st ;
        serviceArea = sa ; 
        toiletArea = ta ;
        wrk = w ;
    }
    
    public String getCustName()
    {
        return custName ;
    }
    public String getPhoneNo()
    {
        return phoneNo ;
    }
    public String getAddress()
    {
        return address ;
    }
    public String getServiceType()
    {
        return serviceType ;
    }
    public double getServiceArea()
    {
        return serviceArea ;
    }
    public double getToiletArea()
    {
        return toiletArea ;
    }
    public Worker getWorker()
    {
        return wrk ;
    }
    
    
    //ans b i)
    public double calcServiceCharge()
    {
        double serviceCharge = 0; 

        if (serviceType.equalsIgnoreCase("Grass"))
            serviceCharge = serviceArea * 0.85;
                 
        else if (serviceType.equalsIgnoreCase("Room"))
        {
            if (toiletArea > 0)
                serviceCharge = toiletArea * 2.00 + (toiletArea * 5.00);
                
            else
                serviceCharge = serviceArea * 2.00;
        }
  
        if (serviceCharge > 150)
            serviceCharge = serviceCharge * 0.97;
        
        return serviceCharge ;
    }
    
    public String toString()
    {
        return custName + phoneNo + address + serviceType + serviceArea + toiletArea + wrk;
    }
}