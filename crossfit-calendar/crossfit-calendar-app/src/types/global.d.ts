declare const crossfitCalendarConfig: {
  whatsappNumber: string;
};


interface Window {
  crossfitCalendarConfig: {
      whatsappNumber: string;
      schedules: any[]; // Cambia 'any[]' por el tipo específico si lo conoces
  };
}
