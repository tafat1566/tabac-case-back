import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { AppComponent } from './app.component';
import { AccueilComponent } from './accueil/accueil.component';
import { BienvenueComponent } from './bienvenue/bienvenue.component';
import { TestComponent } from './test/test.component';

@NgModule({
  declarations: [
    AppComponent,
    AccueilComponent,
    BienvenueComponent,
    TestComponent
  ],
  imports: [
    BrowserModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
